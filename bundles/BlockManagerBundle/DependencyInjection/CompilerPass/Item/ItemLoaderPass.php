<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ItemLoaderPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.item.item_loader';
    const TAG_NAME = 'netgen_block_manager.item.value_loader';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $itemLoader = $container->findDefinition(self::SERVICE_NAME);

        $valueLoaders = array();
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $serviceName => $tag) {
            if (!isset($tag[0]['value_type'])) {
                throw new RuntimeException(
                    "Value loader service definition must have a 'value_type' attribute in its' tag."
                );
            }

            if (!preg_match('/^[A-Za-z]([A-Za-z0-9_])*$/', $tag[0]['value_type'])) {
                throw new RuntimeException(
                    'Value type must begin with a letter and be followed by any combination of letters, digits and underscore.'
                );
            }

            $valueType = $tag[0]['value_type'];

            $valueLoaders[$valueType] = new Reference($serviceName);
        }

        $itemLoader->replaceArgument(1, $valueLoaders);
    }
}
