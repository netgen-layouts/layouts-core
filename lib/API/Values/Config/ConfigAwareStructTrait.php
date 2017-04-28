<?php

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\Exception\Core\ConfigException;

trait ConfigAwareStructTrait
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigStruct[]
     */
    protected $configStructs = array();

    /**
     * Sets the config struct to this struct.
     *
     * @param $identifier
     * @param \Netgen\BlockManager\API\Values\Config\ConfigStruct $configStruct
     */
    public function setConfigStruct($identifier, ConfigStruct $configStruct)
    {
        $this->configStructs[$identifier] = $configStruct;
    }

    /**
     * Returns if the struct has a config struct with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfigStruct($identifier)
    {
        return array_key_exists($identifier, $this->configStructs);
    }

    /**
     * Gets the config struct with provided identifier.
     *
     * @param $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If config struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigStruct
     */
    public function getConfigStruct($identifier)
    {
        if (!$this->hasConfigStruct($identifier)) {
            throw ConfigException::noConfigStruct($identifier);
        }

        return $this->configStructs[$identifier];
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
