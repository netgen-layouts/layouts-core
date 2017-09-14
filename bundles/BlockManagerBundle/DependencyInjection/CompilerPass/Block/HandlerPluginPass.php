<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Block;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HandlerPluginPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.block.registry.handler_plugin';
    const TAG_NAME = 'netgen_block_manager.block.block_definition_handler.plugin';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $handlerPluginRegistry = $container->findDefinition(self::SERVICE_NAME);

        $handlerPlugins = array();
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $handlerPlugin => $tag) {
            $priority = isset($tag[0]['priority']) ? (int) $tag[0]['priority'] : 0;
            $handlerPlugins[$priority][] = new Reference($handlerPlugin);
        }

        krsort($handlerPlugins);
        $handlerPlugins = array_merge(...$handlerPlugins);

        foreach ($handlerPlugins as $handlerPlugin) {
            $handlerPluginRegistry->addMethodCall(
                'addPlugin',
                array($handlerPlugin)
            );
        }
    }
}
