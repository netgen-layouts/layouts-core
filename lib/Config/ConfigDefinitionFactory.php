<?php

namespace Netgen\BlockManager\Config;

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
     * @param string $configKey
     * @param \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface $handler
     *
     * @return \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    public function buildConfigDefinition(
        $configKey,
        ConfigDefinitionHandlerInterface $handler
    ) {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameters = $parameterBuilder->buildParameters();

        return new ConfigDefinition(
            array(
                'configKey' => $configKey,
                'handler' => $handler,
                'parameters' => $parameters,
            )
        );
    }
}
