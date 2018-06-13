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
     *
     * @param string $configKey
     * @param \Netgen\BlockManager\API\Values\Config\ConfigStruct $configStruct
     */
    public function setConfigStruct($configKey, ConfigStruct $configStruct)
    {
        $this->configStructs[$configKey] = $configStruct;
    }

    /**
     * Returns if the struct has a config struct with provided config key.
     *
     * @param string $configKey
     *
     * @return bool
     */
    public function hasConfigStruct($configKey)
    {
        return array_key_exists($configKey, $this->configStructs);
    }

    /**
     * Gets the config struct with provided config key.
     *
     * @param string $configKey
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If config struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct
     */
    public function getConfigStruct($configKey)
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
    public function getConfigStructs()
    {
        return $this->configStructs;
    }
}
