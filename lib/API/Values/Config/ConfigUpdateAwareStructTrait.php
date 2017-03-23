<?php

namespace Netgen\BlockManager\API\Values\Config;

use Netgen\BlockManager\Exception\InvalidArgumentException;

trait ConfigUpdateAwareStructTrait
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigUpdateStruct[]
     */
    protected $configUpdateStructs = array();

    /**
     * Sets the config update struct to this struct.
     *
     * @param $identifier
     * @param \Netgen\BlockManager\API\Values\Config\ConfigUpdateStruct $configUpdateStruct
     */
    public function setConfigUpdateStruct($identifier, ConfigUpdateStruct $configUpdateStruct)
    {
        $this->configUpdateStructs[$identifier] = $configUpdateStruct;
    }

    /**
     * Returns if the struct has a config update struct with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfigUpdateStruct($identifier)
    {
        return array_key_exists($identifier, $this->configUpdateStructs);
    }

    /**
     * Gets the config update struct with provided identifier.
     *
     * @param $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If config update struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigUpdateStruct
     */
    public function getConfigUpdateStruct($identifier)
    {
        if (!$this->hasConfigUpdateStruct($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Config update struct with identifier "%s" does not exist in the struct.',
                    $identifier
                )
            );
        }

        return $this->configUpdateStructs[$identifier];
    }

    /**
     * Returns all config update structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigUpdateStruct[]
     */
    public function getConfigUpdateStructs()
    {
        return $this->configUpdateStructs;
    }
}
