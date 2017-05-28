<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

interface ConfigDefinitionInterface extends ParameterCollectionInterface
{
    /**
     * Returns the type of the config definition.
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the config key for the definition.
     *
     * @return string
     */
    public function getConfigKey();

    /**
     * Returns if this config definition is enabled for current config aware value.
     *
     * @param \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue
     *
     * @return bool
     */
    public function isEnabled(ConfigAwareValue $configAwareValue);
}
