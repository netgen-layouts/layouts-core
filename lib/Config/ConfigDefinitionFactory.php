<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;

class ConfigDefinitionFactory
{
    /**
     * Builds the config definition.
     *
     * @param string $type
     * @param string $identifier
     * @param \Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface $handler
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderInterface $parameterBuilder
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public static function buildConfigDefinition(
        $type,
        $identifier,
        ConfigDefinitionHandlerInterface $handler,
        ParameterBuilderInterface $parameterBuilder
    ) {
        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        return new ConfigDefinition(
            array(
                'type' => $type,
                'identifier' => $identifier,
                'handler' => $handler,
                'parameters' => $parameters,
            )
        );
    }
}
