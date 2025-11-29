<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\Parameters\ParameterList;

final class ConfigMapper
{
    public function __construct(
        private ParameterMapper $parameterMapper,
    ) {}

    /**
     * Maps the provided config array to API values according to provided config definitions.
     *
     * @param array<string, array<string, mixed>> $config
     * @param \Netgen\Layouts\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @return iterable<string, \Netgen\Layouts\API\Values\Config\Config>
     */
    public function mapConfig(array $config, array $configDefinitions): iterable
    {
        foreach ($configDefinitions as $configKey => $configDefinition) {
            yield $configKey => Config::fromArray(
                [
                    'configKey' => $configKey,
                    'definition' => $configDefinition,
                    'parameters' => new ParameterList(
                        [
                            ...$this->parameterMapper->mapParameters(
                                $configDefinition,
                                $config[$configKey] ?? [],
                            ),
                        ],
                    ),
                ],
            );
        }
    }

    /**
     * Serializes the existing config struct values based on provided config definitions.
     *
     * @param \Netgen\Layouts\API\Values\ParameterStruct[] $configStructs
     * @param \Netgen\Layouts\Config\ConfigDefinitionInterface[] $configDefinitions
     * @param array<string, array<string, mixed>> $fallbackValues
     *
     * @return iterable<string, array<string, mixed>>
     */
    public function serializeValues(array $configStructs, array $configDefinitions, array $fallbackValues = []): iterable
    {
        foreach ($configDefinitions as $configKey => $configDefinition) {
            $configValues = [];

            if (($configStructs[$configKey] ?? null) instanceof ParameterStruct) {
                $configValues = $configStructs[$configKey]->parameterValues;
            }

            yield $configKey => [
                ...$this->parameterMapper->serializeValues(
                    $configDefinition,
                    $configValues,
                    $fallbackValues[$configKey] ?? [],
                ),
            ];
        }
    }
}
