<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class QueryTypeRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.collection.registry.query_type';
    const TAG_NAME = 'netgen_block_manager.collection.query_type_handler';

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
        $queryTypeHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        $queryTypes = $container->getParameter('netgen_block_manager.query_types');
        foreach ($queryTypes as $type => $queryType) {
            $configServiceName = sprintf('netgen_block_manager.collection.query_type.configuration.%s', $type);
            $configService = new Definition(
                $container->getParameter('netgen_block_manager.collection.query_type.configuration.class')
            );

            $configService->setArguments(array($type, $queryType));
            $configService->setFactory(
                array(
                    $container->getParameter('netgen_block_manager.collection.query_type.configuration.factory.class'),
                    'buildQueryTypeConfig',
                )
            );

            $container->setDefinition($configServiceName, $configService);

            foreach ($queryTypeHandlers as $queryTypeHandler => $tag) {
                if (!isset($tag[0]['type'])) {
                    throw new RuntimeException(
                        "Query type handler definition must have a 'type' attribute in its' tag."
                    );
                }

                if ($tag[0]['type'] !== $type) {
                    continue;
                }

                $queryTypeServiceName = sprintf('netgen_block_manager.collection.query_type.%s', $type);
                $queryTypeService = new Definition(
                    $container->getParameter('netgen_block_manager.collection.query_type.class')
                );

                $queryTypeService->addArgument($type);
                $queryTypeService->addArgument(new Reference($queryTypeHandler));
                $queryTypeService->addArgument(new Reference($configServiceName));
                $container->setDefinition($queryTypeServiceName, $queryTypeService);

                $queryTypeRegistry->addMethodCall(
                    'addQueryType',
                    array(new Reference($queryTypeServiceName))
                );
            }
        }
    }
}
