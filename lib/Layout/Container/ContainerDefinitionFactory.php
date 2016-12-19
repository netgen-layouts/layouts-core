<?php

namespace Netgen\BlockManager\Layout\Container;

use Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

class ContainerDefinitionFactory
{
    /**
     * Builds the container definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Layout\Container\ContainerDefinition\ContainerDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface
     */
    public static function buildContainerDefinition(
        $identifier,
        ContainerDefinitionHandlerInterface $handler,
        Configuration $config,
        ParameterBuilderInterface $parameterBuilder
    ) {
        $placeholderIdentifiers = $handler->getPlaceholderIdentifiers();

        $placeholders = array();
        $placeholderBuilders = array();

        foreach ($placeholderIdentifiers as $placeholderIdentifier) {
            // Parameter builder is a one use object, hence the clone
            $placeholderBuilders[$placeholderIdentifier] = clone $parameterBuilder;
        }

        $handler->buildPlaceholderParameters($placeholderBuilders);

        foreach ($placeholderIdentifiers as $placeholderIdentifier) {
            $placeholders[$placeholderIdentifier] = new PlaceholderDefinition(
                array(
                    'identifier' => $placeholderIdentifier,
                    'parameters' => $placeholderBuilders[$placeholderIdentifier]->buildParameters(),
                )
            );
        }

        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        return new ContainerDefinition(
            array(
                'identifier' => $identifier,
                'config' => $config,
                'placeholders' => $placeholders,
                'parameters' => $parameters,
            )
        );
    }

    /**
     * Builds the dynamic container definition.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Layout\Container\ContainerDefinition\DynamicContainerDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration\Configuration $config
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return \Netgen\BlockManager\Layout\Container\DynamicContainerDefinitionInterface
     */
    public static function buildDynamicContainerDefinition(
        $identifier,
        DynamicContainerDefinitionHandlerInterface $handler,
        Configuration $config,
        ParameterBuilderInterface $parameterBuilder
    ) {
        // Parameter builder is a one use object, hence the clone
        $placeholderBuilder = clone $parameterBuilder;

        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        $handler->buildDynamicPlaceholderParameters($placeholderBuilder);
        $placeholderParameters = $placeholderBuilder->buildParameters();

        return new DynamicContainerDefinition(
            array(
                'identifier' => $identifier,
                'config' => $config,
                'parameters' => $parameters,
                'dynamicPlaceholder' => new PlaceholderDefinition(
                    array(
                        'identifier' => 'dynamic',
                        'parameters' => $placeholderParameters,
                    )
                ),
            )
        );
    }
}
