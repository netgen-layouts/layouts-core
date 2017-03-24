<?php

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigAwareValue
{
    /**
     * Returns all available configurations.
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getConfigs();

    /**
     * Returns the configuration with specified identifier.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config
     */
    public function getConfig($identifier);

    /**
     * Returns if the configuration with specified identifier exists.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfig($identifier);
}
