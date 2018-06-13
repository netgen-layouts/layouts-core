<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\View;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\ViewBuilderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class ViewBuilderPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\ViewBuilderPass::process
     */
    public function testProcess()
    {
        $viewBuilder = new Definition();
        $viewBuilder->addArgument([]);
        $viewBuilder->addArgument([]);
        $viewBuilder->addArgument([]);
        $this->setDefinition('netgen_block_manager.view.view_builder', $viewBuilder);

        $viewProvider = new Definition();
        $viewProvider->addTag('netgen_block_manager.view.provider');
        $this->setDefinition('netgen_block_manager.view.provider.test', $viewProvider);

        $viewProvider2 = new Definition();
        $viewProvider2->addTag('netgen_block_manager.view.provider');
        $this->setDefinition('netgen_block_manager.view.provider.test2', $viewProvider2);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'netgen_block_manager.view.view_builder',
            2,
            [
                new Reference('netgen_block_manager.view.provider.test'),
                new Reference('netgen_block_manager.view.provider.test2'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View\ViewBuilderPass::process
     */
    public function testProcessWithEmptyContainer()
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    /**
     * Register the compiler pass under test.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ViewBuilderPass());
    }
}
