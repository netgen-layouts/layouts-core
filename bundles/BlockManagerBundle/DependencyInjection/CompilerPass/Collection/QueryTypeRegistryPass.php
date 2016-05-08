<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class QueryTypeRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.collection.query_type.registry';
    const TAG_NAME = 'netgen_block_manager.collection.query_type';

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

        $queryTypeRegistry = $container->findDefinition(self::SERVICE_NAME);
        $queryTypes = array_keys($container->findTaggedServiceIds(self::TAG_NAME));

        foreach ($queryTypes as $queryType) {
            $queryTypeRegistry->addMethodCall(
                'addQueryType',
                array(new Reference($queryType))
            );
        }
    }
}
