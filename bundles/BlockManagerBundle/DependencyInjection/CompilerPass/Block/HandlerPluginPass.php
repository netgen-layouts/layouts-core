<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class HandlerPluginPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.block.registry.handler_plugin';
    private static $tagName = 'netgen_block_manager.block.block_definition_handler.plugin';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $handlerPluginRegistry = $container->findDefinition(self::$serviceName);

        $handlerPlugins = [];
        foreach ($container->findTaggedServiceIds(self::$tagName) as $handlerPlugin => $tag) {
            $priority = isset($tag[0]['priority']) ? (int) $tag[0]['priority'] : 0;
            $handlerPlugins[$priority][] = new Reference($handlerPlugin);
        }

        krsort($handlerPlugins);
        $handlerPlugins = array_merge(...$handlerPlugins);

        foreach ($handlerPlugins as $handlerPlugin) {
            $handlerPluginRegistry->addMethodCall(
                'addPlugin',
                [$handlerPlugin]
            );
        }
    }
}
