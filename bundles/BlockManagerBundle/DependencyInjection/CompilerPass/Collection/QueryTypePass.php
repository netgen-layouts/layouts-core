<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Collection;

use Netgen\BlockManager\Collection\QueryType\QueryType;
use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class QueryTypePass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.collection.registry.query_type';
    private static $tagName = 'netgen_block_manager.collection.query_type_handler';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $queryTypeRegistry = $container->findDefinition(self::$serviceName);
        $queryTypeHandlers = $container->findTaggedServiceIds(self::$tagName);

        $queryTypes = $container->getParameter('netgen_block_manager.query_types');
        foreach ($queryTypes as $type => $queryType) {
            $handlerIdentifier = $type;
            if (!empty($queryType['handler'])) {
                $handlerIdentifier = $queryType['handler'];
            }

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
            $queryTypeService->setPublic(true);
            $queryTypeService->addArgument($type);
            $queryTypeService->addArgument(new Reference($foundHandler));
            $queryTypeService->addArgument($queryType);
            $queryTypeService->setFactory([new Reference('netgen_block_manager.collection.query_type_factory'), 'buildQueryType']);

            $container->setDefinition($queryTypeServiceName, $queryTypeService);

            $queryTypeRegistry->addMethodCall(
                'addQueryType',
                [
                    $type,
                    new Reference($queryTypeServiceName),
                ]
            );
        }
    }
}
