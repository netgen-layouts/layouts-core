<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;

class ConfigDefinitionFactory
{
    /**
     * @var \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface
     */
    protected $parameterBuilderFactory;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface $parameterBuilderFactory
     */
    public function __construct(ParameterBuilderFactoryInterface $parameterBuilderFactory)
    {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
    }

    /**
     * Builds the config definition.
     *
     * @param string $type
     * @param string $identifier
     * @param \Netgen\BlockManager\Config\ConfigDefinition\ConfigDefinitionHandlerInterface $handler
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function buildConfigDefinition(
        $type,
        $identifier,
        ConfigDefinitionHandlerInterface $handler
    ) {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
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
