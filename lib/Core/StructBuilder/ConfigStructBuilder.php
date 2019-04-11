<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\StructBuilder;

use Netgen\Layouts\API\Values\Config\ConfigAwareStruct;
use Netgen\Layouts\API\Values\Config\ConfigAwareValue;
use Netgen\Layouts\API\Values\Config\ConfigStruct;

final class ConfigStructBuilder
{
    /**
     * Fills the provided config aware struct with config structs, according to the provided value.
     */
    public function buildConfigUpdateStructs(ConfigAwareValue $configAwareValue, ConfigAwareStruct $configAwareStruct): void
    {
        foreach ($configAwareValue->getConfigs() as $configKey => $config) {
            $configStruct = new ConfigStruct();
            $configStruct->fillParametersFromConfig($config);
            $configAwareStruct->setConfigStruct($configKey, $configStruct);
        }
    }
}
