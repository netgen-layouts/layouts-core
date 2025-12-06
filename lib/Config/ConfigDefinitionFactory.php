<?php

declare(strict_types=1);

namespace Netgen\Layouts\Config;

use Netgen\Layouts\Parameters\ParameterBuilderFactory;

final class ConfigDefinitionFactory
{
    public function __construct(
        private ParameterBuilderFactory $parameterBuilderFactory,
    ) {}

    /**
     * Builds the config definition.
     */
    public function buildConfigDefinition(
        string $configKey,
        ConfigDefinitionHandlerInterface $handler,
    ): ConfigDefinitionInterface {
        $parameterBuilder = $this->parameterBuilderFactory->createParameterBuilder([], false);
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
