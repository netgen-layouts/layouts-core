<?php

namespace Netgen\BlockManager\Config;

use Netgen\BlockManager\API\Values\Config\ConfigAwareValue;
use Netgen\BlockManager\Parameters\ParameterCollectionInterface;

/**
 * Config definition represents an abstract concept reusable by all
 * entities which allows specification and validation of entity configuration
 * stored in the database. For example, blocks use these definitions
 * to specify how the block HTTP cache config is stored and validated.
 */
interface ConfigDefinitionInterface extends ParameterCollectionInterface
{
    /**
     * Returns the config key for the definition.
     *
     * @return string
     */
    public function getConfigKey();

    /**
     * Returns if this config definition is enabled for provided config aware value.
     *
     * @param \Netgen\BlockManager\API\Values\Config\ConfigAwareValue $configAwareValue
     *
     * @return bool
     */
    public function isEnabled(ConfigAwareValue $configAwareValue);
}
