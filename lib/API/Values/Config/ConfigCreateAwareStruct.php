<?php

namespace Netgen\BlockManager\API\Values\Config;

interface ConfigCreateAwareStruct
{
    /**
     * Sets the config create struct to this struct.
     *
     * @param $identifier
     * @param \Netgen\BlockManager\API\Values\Config\ConfigCreateStruct $configCreateStruct
     */
    public function setConfigCreateStruct($identifier, ConfigCreateStruct $configCreateStruct);

    /**
     * Returns if the struct has a config create struct with provided identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConfigCreateStruct($identifier);

    /**
     * Gets the config create struct with provided identifier.
     *
     * @param $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If config create struct does not exist
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigCreateStruct
     */
    public function getConfigCreateStruct($identifier);

    /**
     * Returns all config create structs from the struct.
     *
     * @return \Netgen\BlockManager\API\Values\Config\ConfigCreateStruct[]
     */
    public function getConfigCreateStructs();
}
