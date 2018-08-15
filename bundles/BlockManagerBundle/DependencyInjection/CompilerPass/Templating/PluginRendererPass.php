<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Templating;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class PluginRendererPass implements CompilerPassInterface
{
    private const SERVICE_NAME = 'netgen_block_manager.templating.plugin_renderer';
    private const TAG_NAME = 'netgen_block_manager.templating.plugin';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $pluginRenderer = $container->findDefinition(self::SERVICE_NAME);
        $pluginServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $pluginsByName = [];
        $sortedPlugins = [];

        foreach ($pluginServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['plugin'])) {
                    throw new RuntimeException(
                        "Template plugin service definition must have an 'plugin' attribute in its' tag."
                    );
                }

                $priority = (int) ($tag['priority'] ?? 0);
                $pluginsByName[$tag['plugin']][$priority][] = new Reference($serviceName);
            }
        }

        foreach ($pluginsByName as $pluginName => $plugins) {
            krsort($plugins);
            $sortedPlugins[$pluginName] = array_merge(...$plugins);
        }

        $pluginRenderer->replaceArgument(1, $sortedPlugins);
    }
}
