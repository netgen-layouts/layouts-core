<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\View;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ViewBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.view.builder';
    const TAG_NAME = 'netgen_block_manager.view.provider';

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

        $viewBuilder = $container->findDefinition(self::SERVICE_NAME);
        $viewProviderServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $viewProviders = array();
        foreach ($viewProviderServices as $serviceName => $tag) {
            $viewProviders[] = new Reference($serviceName);
        }

        $viewBuilder->replaceArgument(0, $viewProviders);
    }
}
