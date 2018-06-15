<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigAwareStruct
{
    /**
     * Sets the config struct to this struct.
     *
     * @param string $configKey
     * @param \Netgen\BlockManager\API\Values\Config\ConfigStruct $configStruct
     */
    public function setConfigStruct(string $configKey, ConfigStruct $configStruct): void;

    /**
     * Returns if the struct has a config struct with provided config key.
     *
     * @param string $configKey
     *
     * @return bool
     */
    public function hasConfigStruct(string $configKey): bool;

    /**
     * Returns all config structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct[]
     */
    public function getConfigStructs(): array;

    /**
     * Gets the config struct with provided config key.
     *
     * @param string $configKey
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If config struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct
     */
    public function getConfigStruct(string $configKey): ConfigStruct;
}
