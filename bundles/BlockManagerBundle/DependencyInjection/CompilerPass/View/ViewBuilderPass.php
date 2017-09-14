<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ViewBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.view.view_builder';
    const TAG_NAME = 'netgen_block_manager.view.provider';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $viewBuilder = $container->findDefinition(self::SERVICE_NAME);
        $viewProviderServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $viewProviders = array();
        foreach ($viewProviderServices as $serviceName => $tag) {
            $priority = isset($tag[0]['priority']) ? (int) $tag[0]['priority'] : 0;
            $viewProviders[$priority][] = new Reference($serviceName);
        }

        krsort($viewProviders);
        $viewProviders = array_merge(...$viewProviders);

        $viewBuilder->replaceArgument(2, $viewProviders);
    }
}
