<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Core\Values\Config\Config;

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
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function mapConfig(array $config, array $configDefinitions)
    {
        $configs = array();

        foreach ($configDefinitions as $configKey => $configDefinition) {
            $parameters = $this->parameterMapper->mapParameters(
                $configDefinition,
                isset($config[$configKey]) ?
                    $config[$configKey] :
                    array()
            );

            $configs[$configKey] = new Config(
                array(
                    'configKey' => $configKey,
                    'definition' => $configDefinition,
                    'parameters' => $parameters,
                )
            );
        }

        return $configs;
    }

    /**
     * Serializes the existing config struct values based on provided config definitions.
     *
     * @param \Netgen\BlockManager\API\Values\ParameterStruct[] $configStructs
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     * @param array $fallbackValues
     *
     * @return array
     */
    public function serializeValues(array $configStructs, array $configDefinitions, array $fallbackValues = array())
    {
        $configs = array();

        foreach ($configDefinitions as $configKey => $configDefinition) {
            $configValues = array();

            if (
                isset($configStructs[$configKey]) &&
                $configStructs[$configKey] instanceof ParameterStruct
            ) {
                $configValues = $configStructs[$configKey]->getParameterValues();
            }

            $configs[$configKey] = $this->parameterMapper->serializeValues(
                $configDefinition,
                $configValues,
                isset($fallbackValues[$configKey]) ?
                    $fallbackValues[$configKey] :
                    array()
            );
        }

        return $configs;
    }
}
