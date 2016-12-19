<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\ContainerType\ContainerType;

interface ContainerTypeRegistryInterface
{
    /**
     * Adds a container type to registry.
     *
     * @param \Netgen\BlockManager\Configuration\ContainerType\ContainerType $containerType
     */
    public function addContainerType(ContainerType $containerType);

    /**
     * Returns if registry has a container type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasContainerType($identifier);

    /**
     * Returns the container type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If container type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Configuration\ContainerType\ContainerType
     */
    public function getContainerType($identifier);

    /**
     * Returns all container types.
     *
     * @return \Netgen\BlockManager\Configuration\ContainerType\ContainerType[]
     */
    public function getContainerTypes();
}
