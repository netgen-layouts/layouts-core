<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Item;

use Netgen\BlockManager\Exception\RuntimeException;
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
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $valueLoaderRegistry = $container->findDefinition(self::SERVICE_NAME);

        $valueTypes = array();
        foreach ($container->findTaggedServiceIds(self::TAG_NAME) as $valueLoader => $tag) {
            if (!isset($tag[0]['value_type'])) {
                throw new RuntimeException(
                    "Value loader service definition must have a 'value_type' attribute in its' tag."
                );
            }

            if (!preg_match('/[A-Za-z]([A-Za-z0-9_])*/', $tag[0]['value_type'])) {
                throw new RuntimeException(
                    'Value type must begin with a letter and be followed by' .
                    'any combination of letters, digits and underscore.'
                );
            }

            $valueLoaderRegistry->addMethodCall(
                'addValueLoader',
                array(new Reference($valueLoader))
            );

            $valueTypes[] = $tag[0]['value_type'];
        }

        $container->setParameter('netgen_block_manager.item.value_types', $valueTypes);
    }
}
