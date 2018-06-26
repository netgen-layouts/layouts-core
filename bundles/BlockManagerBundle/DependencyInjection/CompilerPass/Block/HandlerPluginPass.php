<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HandlerPluginPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.block.registry.handler_plugin';
    private const TAG_NAME = 'netgen_block_manager.block.block_definition_handler.plugin';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $handlerPluginRegistry = $container->findDefinition(self::SERVICE_NAME);

        $handlerPlugins = [];
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $handlerPlugin => $tag) {
            $priority = (int) ($tag[0]['priority'] ?? 0);
            $handlerPlugins[$priority][] = new Reference($handlerPlugin);
        }

        krsort($handlerPlugins);
        $handlerPlugins = array_merge(...$handlerPlugins);

        $handlerPluginRegistry->replaceArgument(0, $handlerPlugins);
    }
}
