<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\API\Values\ParameterStruct;
use Netgen\BlockManager\API\Values\ParameterStructTrait;
use Netgen\BlockManager\Config\ConfigDefinitionInterface;
use Netgen\BlockManager\Value;

final class ConfigStruct extends Value implements ParameterStruct
{
    use ParameterStructTrait;

    /**
     * Fills the struct with the default parameter values as defined in provided
     * config definition.
     */
    public function fillDefaultParameters(ConfigDefinitionInterface $configDefinition): void
    {
        $this->fillDefault($configDefinition);
    }

    /**
     * Fills the parameter values based on provided config.
     */
    public function fillParametersFromConfig(Config $config): void
    {
        $this->fillFromCollection($config->getDefinition(), $config);
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
     */
    public function fillParametersFromHash(ConfigDefinitionInterface $configDefinition, array $values = [], bool $doImport = false): void
    {
        $this->fillFromHash($configDefinition, $values, $doImport);
    }
}
