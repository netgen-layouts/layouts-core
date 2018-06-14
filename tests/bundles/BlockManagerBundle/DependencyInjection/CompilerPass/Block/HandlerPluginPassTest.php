<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\DependencyInjection\CompilerPass\Block;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\HandlerPluginPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
use Symfony\Component\DependencyInjection\Reference;

final class HandlerPluginPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\HandlerPluginPass::process
     */
    public function testProcess(): void
    {
        $this->setDefinition('netgen_block_manager.block.registry.handler_plugin', new Definition());

        $handlerPlugin = new Definition();
        $handlerPlugin->addTag('netgen_block_manager.block.block_definition_handler.plugin');
        $this->setDefinition('netgen_block_manager.block.block_definition_handler.plugin.test', $handlerPlugin);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'netgen_block_manager.block.registry.handler_plugin',
            'addPlugin',
            [
                new Reference('netgen_block_manager.block.block_definition_handler.plugin.test'),
            ]
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block\HandlerPluginPass::process
     */
    public function testProcessWithEmptyContainer(): void
    {
        $this->compile();

        $this->assertInstanceOf(FrozenParameterBag::class, $this->container->getParameterBag());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HandlerPluginPass());
    }
}
