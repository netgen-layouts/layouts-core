<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class HandlerPluginPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private const SERVICE_NAME = 'netgen_block_manager.block.registry.handler_plugin';
    private const TAG_NAME = 'netgen_block_manager.block.block_definition_handler.plugin';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $handlerPluginRegistry = $container->findDefinition(self::SERVICE_NAME);
        $handlerPlugins = $this->findAndSortTaggedServices(self::TAG_NAME, $container);

        $handlerPluginRegistry->replaceArgument(0, $handlerPlugins);
    }
}
