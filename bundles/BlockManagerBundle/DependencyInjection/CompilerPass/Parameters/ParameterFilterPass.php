<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Netgen\BlockManager\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ParameterFilterPass implements CompilerPassInterface
{
    private static $serviceName = 'netgen_block_manager.parameters.registry.parameter_filter';
    private static $tagName = 'netgen_block_manager.parameters.parameter_filter';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::$serviceName)) {
            return;
        }

        $parameterFilterRegistry = $container->findDefinition(self::$serviceName);
        $parameterFilters = $container->findTaggedServiceIds(self::$tagName);

        uasort(
            $parameterFilters,
            function (array $a, array $b) {
                return ($b[0]['priority'] ?? 0) <=> ($a[0]['priority'] ?? 0);
            }
        );

        $parameterFiltersPerType = [];
        foreach ($parameterFilters as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['type'])) {
                    throw new RuntimeException(
                        "Parameter filter service definition must have a 'type' attribute in its' tag."
                    );
                }

                $parameterFiltersPerType[$tag['type']][] = new Reference($serviceName);
            }
        }

        foreach ($parameterFiltersPerType as $type => $filters) {
            foreach ($filters as $filter) {
                $parameterFilterRegistry->addMethodCall(
                    'addParameterFilter',
                    [$type, $filter]
                );
            }
        }
    }
}
