<?php

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Value;

final class ConfigStruct extends Value implements ParameterStruct
{
    use ParameterStructTrait;

    /**
     * Sets the provided parameter values to the struct.
     *
     * The values need to be in the domain format of the value for the parameter.
     *
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface $configDefinition
     * @param array $values
     */
    public function fillParameters(ConfigDefinitionInterface $configDefinition, array $values = array())
    {
        $this->fill($configDefinition, $values);
    }

    /**
     * Fills the parameter values based on provided config.
     *
     * @param \Netgen\BlockManager\API\Values\Config\Config $config
     */
    public function fillParametersFromConfig(Config $config)
    {
        $this->fillFromValue($config->getDefinition(), $config);
    }

    /**
     * Fills the parameter values based on provided array of values.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * If $doImport is set to true, the values will be considered as coming from an import,
     * meaning it will be processed using ParameterTypeInterface::import method instead of
     * ParameterTypeInterface::fromHash method.
     *
     * @param \Netgen\BlockManager\Config\ConfigDefinitionInterface $configDefinition
     * @param array $values
     * @param bool $doImport
     */
    public function fillParametersFromHash(ConfigDefinitionInterface $configDefinition, array $values = array(), $doImport = false)
    {
        $this->fillFromHash($configDefinition, $values, $doImport);
    }
}
