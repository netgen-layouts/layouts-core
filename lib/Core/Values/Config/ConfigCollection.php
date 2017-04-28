<?php

namespace Netgen\BlockManager\Core\Values\Config;

use Netgen\BlockManager\API\Values\Config\ConfigCollection as APIConfigCollection;
use Netgen\BlockManager\Exception\Core\ConfigException;
use Netgen\BlockManager\ValueObject;

class ConfigCollection extends ValueObject implements APIConfigCollection
{
    /**
     * @var string
     */
    protected $configType;

    /**
     * @var \Netgen\BlockManager\API\Values\Config\Config[]
     */
    protected $configs = array();

    /**
     * Returns the config type.
     *
     * @return string
     */
    public function getConfigType()
    {
        return $this->configType;
    }

    /**
     * Returns all available configs.
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * Returns the config with specified identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Core\ConfigException If the config does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config
     */
    public function getConfig($identifier)
    {
        if (isset($this->configs[$identifier])) {
            return $this->configs[$identifier];
        }

        throw ConfigException::noConfig($identifier);
    }

    /**
     * Returns if the config with specified identifier exists.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfig($identifier)
    {
        return isset($this->configs[$identifier]);
    }
}
