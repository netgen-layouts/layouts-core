<?php

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigAwareValue
{
    /**
     * Returns all available configs.
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getConfigs();

    /**
     * Returns the config with specified identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If the config does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config
     */
    public function getConfig($identifier);

    /**
     * Returns if the config with specified identifier exists.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfig($identifier);
}
