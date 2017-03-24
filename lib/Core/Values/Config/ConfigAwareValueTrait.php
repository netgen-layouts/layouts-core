<?php

namespace Netgen\BlockManager\Core\Values\Config;

trait ConfigAwareValueTrait
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigCollection
     */
    protected $configCollection;

    /**
     * Returns the config collection.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigCollection
     */
    public function getConfigCollection()
    {
        return $this->configCollection;
    }

    /**
     * Returns all available configs.
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getConfigs()
    {
        return $this->configCollection->getConfigs();
    }

    /**
     * Returns the config with specified identifier.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config
     */
    public function getConfig($identifier)
    {
        return $this->configCollection->getConfig($identifier);
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
        return $this->configCollection->hasConfig($identifier);
    }
}
