<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class TemplateResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     */
    public function testProcess(): void
    {
        $templateResolver = new Definition();
        $templateResolver->addArgument([]);
        $this->setDefinition('netgen_block_manager.view.template_resolver', $templateResolver);

        $matcher = new Definition();
        $matcher->addTag('netgen_block_manager.view.template_matcher', ['identifier' => 'block_type']);
        $this->setDefinition('netgen_block_manager.view.template_matcher.test', $matcher);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.view.template_resolver',
            0,
            [
                'block_type' => new Reference('netgen_block_manager.view.template_matcher.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     * @expectedException \Netgen\BlockManager\Exception\RuntimeException
     * @expectedExceptionMessage Matcher service definition must have an 'identifier' attribute in its' tag.
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier(): void
    {
        $templateResolver = new Definition();
        $templateResolver->addArgument([]);
        $this->setDefinition('netgen_block_manager.view.template_resolver', $templateResolver);

        $matcher = new Definition();
        $matcher->addTag('netgen_block_manager.view.template_matcher');
        $this->setDefinition('netgen_block_manager.view.template_matcher.test', $matcher);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TemplateResolverPass());
    }
}
