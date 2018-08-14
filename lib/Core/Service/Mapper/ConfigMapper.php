<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Core\Service\Mapper;

use Generator;
use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\ParameterStruct;

final class ConfigMapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    private $parameterMapper;

    public function __construct(ParameterMapper $parameterMapper)
    {
        $this->parameterMapper = $parameterMapper;
    }

    /**
     * Maps the provided config array to API values according to provided config definitions.
     *
     * @param array $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
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
     * @param \Netgen\BlockManager\API\Values\ParameterStruct[] $configStructs
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     * @param array $fallbackValues
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
