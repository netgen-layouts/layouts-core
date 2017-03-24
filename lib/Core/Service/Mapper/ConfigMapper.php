<?php

namespace Netgen\BlockManager\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistryInterface;
use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Core\Values\Config\ConfigCollection;

class ConfigMapper extends Mapper
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper
     */
    protected $parameterMapper;

    /**
     * @var \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistryInterface
     */
    protected $configDefinitionRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Core\Service\Mapper\ParameterMapper $parameterMapper
     * @param \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistryInterface $configDefinitionRegistry
     */
    public function __construct(
        ParameterMapper $parameterMapper,
        ConfigDefinitionRegistryInterface $configDefinitionRegistry
    ) {
        $this->parameterMapper = $parameterMapper;
        $this->configDefinitionRegistry = $configDefinitionRegistry;
    }

    /**
     * Maps the provided config to API values.
     *
     * @param string $type
     * @param array $config
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigCollection
     */
    public function mapConfig($type, array $config)
    {
        $configs = array();

        $configDefinitions = $this->configDefinitionRegistry->getConfigDefinitions($type);
        foreach ($configDefinitions as $identifier => $configDefinition) {
            $parameters = $this->parameterMapper->mapParameters(
                $configDefinition,
                isset($config[$identifier]) ?
                    $config[$identifier] :
                    array()
            );

            $configs[$identifier] = new Config(
                array(
                    'identifier' => $identifier,
                    'definition' => $configDefinition,
                    'parameters' => $parameters,
                )
            );
        }

        return new ConfigCollection(
            array(
                'configType' => $type,
                'configs' => $configs,
            )
        );
    }

    /**
     * Serializes the existing struct values based on provided parameters.
     *
     * @param string $type
     * @param \Netgen\BlockManager\API\Values\ParameterStruct[] $configStructs
     *
     * @return array
     */
    public function serializeValues($type, array $configStructs)
    {
        $configs = array();
        $configDefinitions = $this->configDefinitionRegistry->getConfigDefinitions($type);

        foreach ($configDefinitions as $identifier => $configDefinition) {
            $configValues = array();

            if (
                isset($configStructs[$identifier]) &&
                $configStructs[$identifier] instanceof ParameterStruct
            ) {
                $configValues = $configStructs[$identifier]->getParameterValues();
            }

            $configs[$identifier] = $this->parameterMapper->serializeValues(
                $configDefinition,
                $configValues
            );
        }

        return $configs;
    }
}
