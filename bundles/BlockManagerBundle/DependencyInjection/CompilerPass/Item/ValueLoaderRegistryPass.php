<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ValueLoaderRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.item.registry.value_loader';
    const TAG_NAME = 'netgen_block_manager.item.value_loader';

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

        $valueLoaderRegistry = $container->findDefinition(self::SERVICE_NAME);
        $valueLoaders = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($valueLoaders as $valueLoader) {
            $valueLoaderRegistry->addMethodCall(
                'addValueLoader',
                array(new Reference($valueLoader))
            );
        }
    }
}
