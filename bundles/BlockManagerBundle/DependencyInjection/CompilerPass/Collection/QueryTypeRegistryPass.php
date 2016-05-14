<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class QueryTypeRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.collection.registry.query_type';
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
        $queryTypes = $container->findTaggedServiceIds(self::TAG_NAME);

        foreach ($queryTypes as $queryType => $tag) {
            if (!isset($tag[0]['identifier'])) {
                throw new RuntimeException(
                    "Query type service definition must have an 'identifier' attribute in its' tag."
                );
            }

            $configService = sprintf('netgen_block_manager.configuration.query_type.%s', $tag[0]['identifier']);
            if (!$container->has($configService)) {
                throw new RuntimeException(
                    sprintf('Query type "%s" does not have a configuration.', $tag[0]['identifier'])
                );
            }

            $queryTypeService = $container->findDefinition($queryType);
            $queryTypeService->addMethodCall('setConfiguration', array(new Reference($configService)));

            $queryTypeRegistry->addMethodCall(
                'addQueryType',
                array(new Reference($queryType))
            );
        }
    }
}
