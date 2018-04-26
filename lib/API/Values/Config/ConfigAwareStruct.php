<?php

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigAwareStruct
{
    /**
     * Sets the config struct to this struct.
     *
     * @param string $configKey
     * @param \Netgen\BlockManager\API\Values\Config\ConfigStruct $configStruct
     */
    public function setConfigStruct($configKey, ConfigStruct $configStruct);

    /**
     * Returns if the struct has a config struct with provided config key.
     *
     * @param string $configKey
     *
     * @return bool
     */
    public function hasConfigStruct($configKey);

    /**
     * Returns all config structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct[]
     */
    public function getConfigStructs();

    /**
     * Gets the config struct with provided config key.
     *
     * @param string $configKey
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If config struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct
     */
    public function getConfigStruct($configKey);
}
