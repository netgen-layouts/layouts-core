<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ViewBuilderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.view.builder';

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

        $viewProviderServices = $container->findTaggedServiceIds('netgen_block_manager.view.provider');
        $viewProviders = array();

        foreach ($viewProviderServices as $serviceName => $tag) {
            $viewProviders[] = new Reference($serviceName);
        }

        $templateResolverServices = $container->findTaggedServiceIds('netgen_block_manager.view.template_resolver');
        $templateResolvers = array();

        foreach ($templateResolverServices as $serviceName => $tag) {
            $templateResolvers[] = new Reference($serviceName);
        }

        $viewBuilder->replaceArgument(0, $viewProviders);
        $viewBuilder->replaceArgument(1, $templateResolvers);
    }
}
