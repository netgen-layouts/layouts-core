<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Generator;
use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\ParameterStruct;

final class ConfigMapper
{
    /**
     * @var \Netgen\Layouts\Core\Mapper\ParameterMapper
     */
    private $parameterMapper;

    public function __construct(ParameterMapper $parameterMapper)
    {
        $this->parameterMapper = $parameterMapper;
    }

    /**
     * Maps the provided config array to API values according to provided config definitions.
     *
     * @param array<string, array<string, mixed>> $config
     * @param \Netgen\Layouts\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @return \Generator
     */
    public function mapConfig(array $config, array $configDefinitions): Generator
    {
        foreach ($configDefinitions as $configKey => $configDefinition) {
            yield $configKey => Config::fromArray(
                [
                    'configKey' => $configKey,
                    'definition' => $configDefinition,
                    'parameters' => iterator_to_array(
                        $this->parameterMapper->mapParameters(
                            $configDefinition,
                            $config[$configKey] ?? []
                        )
                    ),
                ]
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
     * @return \Generator
     */
    public function serializeValues(array $configStructs, array $configDefinitions, array $fallbackValues = []): Generator
    {
        foreach ($configDefinitions as $configKey => $configDefinition) {
            $configValues = [];

            if (
                isset($configStructs[$configKey]) &&
                $configStructs[$configKey] instanceof ParameterStruct
            ) {
                $configValues = $configStructs[$configKey]->getParameterValues();
            }

            yield $configKey => iterator_to_array(
                $this->parameterMapper->serializeValues(
                    $configDefinition,
                    $configValues,
                    $fallbackValues[$configKey] ?? []
                )
            );
        }
    }
}
