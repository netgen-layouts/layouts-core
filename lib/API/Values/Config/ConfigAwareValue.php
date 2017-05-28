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
     * Returns the config with specified config key.
     *
     * @param string $configKey
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If the config does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config
     */
    public function getConfig($configKey);

    /**
     * Returns if the config with specified config key exists.
     *
     * @param string $configKey
     *
     * @return bool
     */
    public function hasConfig($configKey);
}
