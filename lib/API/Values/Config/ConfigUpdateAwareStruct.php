<?php

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigUpdateAwareStruct
{
    /**
     * Sets the config update struct to this struct.
     *
     * @param $identifier
     * @param \Netgen\BlockManager\API\Values\Config\ConfigUpdateStruct $configStruct
     */
    public function setConfigUpdateStruct($identifier, ConfigUpdateStruct $configStruct);

    /**
     * Returns if the struct has a config update struct with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfigUpdateStruct($identifier);

    /**
     * Gets the config update struct with provided identifier.
     *
     * @param $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If config update struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigUpdateStruct
     */
    public function getConfigUpdateStruct($identifier);

    /**
     * Returns all config update structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigUpdateStruct[]
     */
    public function getConfigUpdateStructs();
}
