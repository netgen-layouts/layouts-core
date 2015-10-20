<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ViewBuilderRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.registry.view_builder';
    const TAG_NAME = 'netgen_block_manager.view.builder';

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

        $viewBuilderRegistry = $container->findDefinition(self::SERVICE_NAME);
        $viewBuilders = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($viewBuilders as $serviceName => $tag) {
            $viewBuilderRegistry->addMethodCall(
                'addViewBuilder',
                array(new Reference($serviceName), $tag[0]['type'])
            );
        }
    }
}
