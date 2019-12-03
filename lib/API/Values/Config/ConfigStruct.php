<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Config;

use Netgen\Layouts\API\Values\ParameterStruct;
use Netgen\Layouts\API\Values\ParameterStructTrait;
use Netgen\Layouts\Config\ConfigDefinitionInterface;

final class ConfigStruct implements ParameterStruct
{
    use ParameterStructTrait;

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
     * If any of the parameters is missing from the input array, the default value
     * based on parameter definition from the config definition will be used.
     *
     * The values in the array need to be in hash format of the value
     * i.e. the format acceptable by the ParameterTypeInterface::fromHash method.
     *
     * If $doImport is set to true, the values will be considered as coming from an import,
     * meaning it will be processed using ParameterTypeInterface::import method instead of
     * ParameterTypeInterface::fromHash method.
     *
     * @param array<string, mixed> $values
     */
    public function fillParametersFromHash(ConfigDefinitionInterface $configDefinition, array $values, bool $doImport = false): void
    {
        $this->fillFromHash($configDefinition, $values, $doImport);
    }
}
