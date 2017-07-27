<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Locale;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ChainedLocaleContextPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.locale.context.chained';
    const TAG_NAME = 'netgen_block_manager.locale.context';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $chainedLocaleContext = $container->findDefinition(self::SERVICE_NAME);
        $localeContextServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $localeContexts = array();
        foreach ($localeContextServices as $serviceName => $tag) {
            $priority = isset($tag[0]['priority']) ? (int) $tag[0]['priority'] : 0;
            $localeContexts[$priority][] = new Reference($serviceName);
        }

        krsort($localeContexts);
        $localeContexts = array_merge(...$localeContexts);

        $chainedLocaleContext->replaceArgument(0, $localeContexts);
    }
}
