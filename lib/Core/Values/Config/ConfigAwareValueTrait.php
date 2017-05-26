<?php

namespace Netgen\BlockManager\Core\Values\Config;

use Netgen\BlockManager\Exception\Core\ConfigException;

trait ConfigAwareValueTrait
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\Config[]
     */
    protected $configs;

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
