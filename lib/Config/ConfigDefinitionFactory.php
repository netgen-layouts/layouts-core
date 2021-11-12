<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

use Netgen\Layouts\Parameters\ParameterBuilderFactoryInterface;

final class ConfigDefinitionFactory
{
    private ParameterBuilderFactoryInterface $parameterBuilderFactory;

    public function __construct(ParameterBuilderFactoryInterface $parameterBuilderFactory)
    {
        $this->parameterBuilderFactory = $parameterBuilderFactory;
    }

    /**
     * Builds the config definition.
     */
    public function buildConfigDefinition(
        string $configKey,
        ConfigDefinitionHandlerInterface $handler
    ): ConfigDefinitionInterface {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder();
        $handler->buildParameters($parameterBuilder);
        $parameterDefinitions = $parameterBuilder->buildParameterDefinitions();

        return ConfigDefinition::fromArray(
            [
                'configKey' => $configKey,
                'parameterDefinitions' => $parameterDefinitions,
            ],
        );
    }
}
