<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\Collection;

use Netgen\BlockManager\Collection\QueryType\QueryType;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\Bundle\LayoutsBundle\DependencyInjection\CompilerPass\DefinitionClassTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class QueryTypePass implements CompilerPassInterface
{
    use DefinitionClassTrait;

    private const SERVICE_NAME = 'netgen_block_manager.collection.registry.query_type';
    private const TAG_NAME = 'netgen_block_manager.collection.query_type_handler';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $queryTypeRegistry = $container->findDefinition(self::SERVICE_NAME);
        $queryTypeHandlers = $container->findTaggedServiceIds(self::TAG_NAME);
        $queryTypeServices = [];

        $queryTypes = $container->getParameter('netgen_layouts.query_types');
        foreach ($queryTypes as $type => $queryType) {
            $handlerIdentifier = $queryType['handler'] ?? $type;
            $foundHandler = null;

            foreach ($queryTypeHandlers as $queryTypeHandler => $tags) {
                $handlerClass = $this->getDefinitionClass($container, $queryTypeHandler);

                foreach ($tags as $tag) {
                    if (isset($tag['type']) && $tag['type'] === $handlerIdentifier) {
                        $foundHandler = $queryTypeHandler;

                        break 2;
                    }
                }

                if (isset($handlerClass::$defaultType)) {
                    if ($handlerClass::$defaultType === $handlerIdentifier) {
                        $foundHandler = $queryTypeHandler;

                        break;
                    }
                }
            }

            if (!is_string($foundHandler)) {
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
            $queryTypeService->setPublic(false);
            $queryTypeService->addArgument($type);
            $queryTypeService->addArgument(new Reference($foundHandler));
            $queryTypeService->addArgument($queryType);
            $queryTypeService->setFactory([new Reference('netgen_block_manager.collection.query_type_factory'), 'buildQueryType']);

            $container->setDefinition($queryTypeServiceName, $queryTypeService);

            $queryTypeServices[$type] = new Reference($queryTypeServiceName);
        }

        $queryTypeRegistry->replaceArgument(0, $queryTypeServices);
    }
}
