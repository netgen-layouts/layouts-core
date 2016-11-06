<?php

namespace Netgen\Bundle\BlockManagerBundle\DependencyInjection\CompilerPass\Parameters;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Netgen\BlockManager\Exception\RuntimeException;

class ParameterFilterRegistryPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_block_manager.parameters.registry.parameter_filter';
    const TAG_NAME = 'netgen_block_manager.parameters.parameter_filter';

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

        $parameterFilterRegistry = $container->findDefinition(self::SERVICE_NAME);
        $parameterFilters = $container->findTaggedServiceIds(self::TAG_NAME);

        uasort(
            $parameterFilters,
            function ($a, $b) {
                $a[0]['priority'] = isset($a[0]['priority']) ? $a[0]['priority'] : 0;
                $b[0]['priority'] = isset($b[0]['priority']) ? $b[0]['priority'] : 0;

                return $b[0]['priority'] - $a[0]['priority'];
            }
        );

        $parameterFiltersPerType = array();
        foreach ($parameterFilters as $serviceName => $tag) {
            if (!isset($tag[0]['type'])) {
                throw new RuntimeException(
                    "Parameter filter service definition must have a 'type' attribute in its' tag."
                );
            }

            $parameterFiltersPerType[$tag[0]['type']][] = new Reference($serviceName);
        }

        foreach ($parameterFiltersPerType as $type => $filters) {
            $parameterFilterRegistry->addMethodCall(
                'addParameterFilters',
                array($type, $filters)
            );
        }
    }
}
