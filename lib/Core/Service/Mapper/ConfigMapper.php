<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Core\Values\Config\Config;

class ConfigMapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     */
    public function __construct(ParameterMapper $parameterMapper)
    {
        $this->parameterMapper = $parameterMapper;
    }

    /**
     * Maps the provided config to API values.
     *
     * @param array $config
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface[] $configDefinitions
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function mapConfig(array $config, array $configDefinitions)
    {
        $configs = array();

        foreach ($configDefinitions as $configDefinition) {
            $configIdentifier = $configDefinition->getIdentifier();
            $parameters = $this->parameterMapper->mapParameters(
                $configDefinition,
                isset($config[$configIdentifier]) ?
                    $config[$configIdentifier] :
                    array()
            );

            $configs[$configIdentifier] = new Config(
                array(
                    'identifier' => $configIdentifier,
                    'definition' => $configDefinition,
                    'parameters' => $parameters,
                )
            );
        }

        return $configs;
    }

    /**
     * Serializes the existing struct values based on provided parameters.
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

        foreach ($configDefinitions as $configDefinition) {
            $configValues = array();
            $configIdentifier = $configDefinition->getIdentifier();

            if (
                isset($configStructs[$configIdentifier]) &&
                $configStructs[$configIdentifier] instanceof ParameterStruct
            ) {
                $configValues = $configStructs[$configIdentifier]->getParameterValues();
            }

            $configs[$configIdentifier] = $this->parameterMapper->serializeValues(
                $configDefinition,
                $configValues,
                isset($fallbackValues[$configIdentifier]) ?
                    $fallbackValues[$configIdentifier] :
                    array()
            );
        }

        return $configs;
    }
}
