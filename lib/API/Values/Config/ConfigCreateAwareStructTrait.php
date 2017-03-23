<?php

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\Exception\InvalidArgumentException;

trait ConfigCreateAwareStructTrait
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigCreateStruct[]
     */
    protected $configCreateStructs = array();

    /**
     * Sets the config create struct to this struct.
     *
     * @param $identifier
     * @param \Netgen\BlockManager\API\Values\Config\ConfigCreateStruct $configCreateStruct
     */
    public function setConfigCreateStruct($identifier, ConfigCreateStruct $configCreateStruct)
    {
        $this->configCreateStructs[$identifier] = $configCreateStruct;
    }

    /**
     * Returns if the struct has a config create struct with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfigCreateStruct($identifier)
    {
        return array_key_exists($identifier, $this->configCreateStructs);
    }

    /**
     * Gets the config create struct with provided identifier.
     *
     * @param $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If config create struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigCreateStruct
     */
    public function getConfigCreateStruct($identifier)
    {
        if (!$this->hasConfigCreateStruct($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Config create struct with identifier "%s" does not exist in the struct.',
                    $identifier
                )
            );
        }

        return $this->configCreateStructs[$identifier];
    }

    /**
     * Returns all config create structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigCreateStruct[]
     */
    public function getConfigCreateStructs()
    {
        return $this->configCreateStructs;
    }
}
