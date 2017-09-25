<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Netgen\BlockManager\Collection\QueryType;
use Netgen\BlockManager\Collection\QueryType\Configuration\Configuration;
use Netgen\BlockManager\Collection\QueryType\Configuration\Factory;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class QueryTypePass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.collection.registry.query_type';
    const TAG_NAME = 'netgen_block_manager.collection.query_type_handler';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $queryTypeRegistry = $container->findDefinition(self::SERVICE_NAME);
        $queryTypeHandlers = $container->findTaggedServiceIds(self::TAG_NAME);

        $queryTypes = $container->getParameter('netgen_block_manager.query_types');
        foreach ($queryTypes as $type => $queryType) {
            $handlerIdentifier = $type;
            if (!empty($queryType['handler'])) {
                $handlerIdentifier = $queryType['handler'];
            }

            $configServiceName = sprintf('netgen_block_manager.collection.query_type.configuration.%s', $type);
            $configService = new Definition(Configuration::class);

            $configService->setArguments(array($type, $queryType));
            $configService->setPublic(false);
            $configService->setFactory(array(Factory::class, 'buildConfig'));

            $container->setDefinition($configServiceName, $configService);

            $foundHandler = null;
            foreach ($queryTypeHandlers as $queryTypeHandler => $tags) {
                foreach ($tags as $tag) {
                    if (!isset($tag['type'])) {
                        throw new RuntimeException(
                            "Query type handler definition must have a 'type' attribute in its' tag."
                        );
                    }

                    if ($tag['type'] === $handlerIdentifier) {
                        $foundHandler = $queryTypeHandler;
                        break 2;
                    }
                }
            }

            if ($foundHandler === null) {
                throw new RuntimeException(
                    sprintf(
                        'Query type handler for "%s" query type does not exist.',
                        $type
                    )
                );
            }

            $queryTypeServiceName = sprintf('netgen_block_manager.collection.query_type.%s', $type);
            $queryTypeService = new Definition(QueryType::class);

            $queryTypeService->setLazy(true);
            $queryTypeService->addArgument($type);
            $queryTypeService->addArgument(new Reference($foundHandler));
            $queryTypeService->addArgument(new Reference($configServiceName));
            $queryTypeService->setFactory(array(new Reference('netgen_block_manager.collection.query_type_factory'), 'buildQueryType'));

            $container->setDefinition($queryTypeServiceName, $queryTypeService);

            $queryTypeRegistry->addMethodCall(
                'addQueryType',
                array(
                    $type,
                    new Reference($queryTypeServiceName),
                )
            );
        }
    }
}
