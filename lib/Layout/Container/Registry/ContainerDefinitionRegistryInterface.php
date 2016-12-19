<?php

namespace Netgen\BlockManager\Layout\Container\Registry;

use Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface;

interface ContainerDefinitionRegistryInterface
{
    /**
     * Adds a container definition to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface $containerDefinition
     */
    public function addContainerDefinition($identifier, ContainerDefinitionInterface $containerDefinition);

    /**
     * Returns if registry has a container definition.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasContainerDefinition($identifier);

    /**
     * Returns a container definition with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If container definition does not exist
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface
     */
    public function getContainerDefinition($identifier);

    /**
     * Returns all container definitions.
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface[]
     */
    public function getContainerDefinitions();
}
