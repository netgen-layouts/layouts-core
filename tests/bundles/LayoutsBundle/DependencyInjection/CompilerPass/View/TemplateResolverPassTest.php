<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class TemplateResolverPassTest extends AbstractContainerBuilderTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->addCompilerPass(new TemplateResolverPass());
    }

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
            1,
            new Definition(
                ServiceLocator::class,
                [
                    [
                        'block_type' => new ServiceClosureArgument(new Reference('netgen_layouts.view.template_matcher.test')),
                    ],
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\View\TemplateResolverPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        self::assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }
}
