<?php

namespace Netgen\BlockManager\Core\Values\Config;

use Netgen\BlockManager\Exception\InvalidArgumentException;

trait ConfigAwareValueTrait
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\Config[]
     */
    protected $configs = array();

    /**
     * Returns all available configurations.
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config[]
     */
    public function getAllConfigs()
    {
        return $this->configs;
    }

    /**
     * Returns the configuration with specified identifier.
     *
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\API\Values\Config\Config
     */
    public function getConfig($identifier)
    {
        if (isset($this->configs[$identifier])) {
            return $this->configs[$identifier];
        }

        throw new InvalidArgumentException(
            'identifier',
            sprintf(
                'Configuration with "%s" identifier does not exist.',
                $identifier
            )
        );
    }

    /**
     * Returns if the configuration with specified identifier exists.
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
