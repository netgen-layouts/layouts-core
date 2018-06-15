<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\Exception\Core\ConfigException;

trait ConfigAwareStructTrait
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigStruct[]
     */
    protected $configStructs = [];

    /**
     * Sets the config struct to this struct.
     */
    public function setConfigStruct(string $configKey, ConfigStruct $configStruct): void
    {
        $this->configStructs[$configKey] = $configStruct;
    }

    /**
     * Returns if the struct has a config struct with provided config key.
     */
    public function hasConfigStruct(string $configKey): bool
    {
        return array_key_exists($configKey, $this->configStructs);
    }

    /**
     * Gets the config struct with provided config key.
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If config struct does not exist
     */
    public function getConfigStruct(string $configKey): ConfigStruct
    {
        if (!$this->hasConfigStruct($configKey)) {
            throw ConfigException::noConfigStruct($configKey);
        }

        return $this->configStructs[$configKey];
    }

    /**
     * Returns all config structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct[]
     */
    public function getConfigStructs(): array
    {
        return $this->configStructs;
    }
}
