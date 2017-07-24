<?php

namespace Netgen\BlockManager\Core\Service\StructBuilder;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStruct;
use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;

class ConfigStructBuilder
{
    /**
     * Creates new config structs.
     *
     * @param \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue
     * @param \Netgen\BlockManager\API\Values\Config\ConfigAwareStruct $configAwareStruct
     */
    public function buildConfigUpdateStructs(ConfigAwareValue $configAwareValue, ConfigAwareStruct $configAwareStruct)
    {
        foreach ($configAwareValue->getConfigs() as $configIdentifier => $config) {
            $configStruct = new ConfigStruct();
            $configStruct->fillFromValue($config->getDefinition(), $config);
            $configAwareStruct->setConfigStruct($configIdentifier, $configStruct);
        }
    }
}
