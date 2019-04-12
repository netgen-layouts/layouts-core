<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass;
use Netgen\Layouts\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class TemplateResolverPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     */
    public function testProcess(): void
    {
        $templateResolver = new Definition();
        $templateResolver->addArgument([]);
        $this->setDefinition('netgen_layouts.view.template_resolver', $templateResolver);

        $matcher = new Definition();
        $matcher->addTag('netgen_layouts.view_matcher', ['identifier' => 'block_type']);
        $this->setDefinition('netgen_layouts.view.template_matcher.test', $matcher);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_layouts.view.template_resolver',
            0,
            [
                'block_type' => new Reference('netgen_layouts.view.template_matcher.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     */
    public function testProcessThrowsExceptionWithNoTagIdentifier(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Matcher service definition must have an \'identifier\' attribute in its\' tag.');

        $templateResolver = new Definition();
        $templateResolver->addArgument([]);
        $this->setDefinition('netgen_layouts.view.template_resolver', $templateResolver);

        $matcher = new Definition();
        $matcher->addTag('netgen_layouts.view_matcher');
        $this->setDefinition('netgen_layouts.view.template_matcher.test', $matcher);

        $this->compile();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TemplateResolverPass());
    }
}
