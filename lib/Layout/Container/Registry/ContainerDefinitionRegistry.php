<?php

namespace Netgen\BlockManager\Layout\Container\Registry;

use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface;

class ContainerDefinitionRegistry implements ContainerDefinitionRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface[]
     */
    protected $containerDefinitions = array();

    /**
     * Adds a container definition to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface $containerDefinition
     */
    public function addContainerDefinition($identifier, ContainerDefinitionInterface $containerDefinition)
    {
        $this->containerDefinitions[$identifier] = $containerDefinition;
    }

    /**
     * Returns if registry has a container definition.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasContainerDefinition($identifier)
    {
        return isset($this->containerDefinitions[$identifier]);
    }

    /**
     * Returns a container definition with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If container definition does not exist
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface
     */
    public function getContainerDefinition($identifier)
    {
        if (!$this->hasContainerDefinition($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Container definition with "%s" identifier does not exist.',
                    $identifier
                )
            );
        }

        return $this->containerDefinitions[$identifier];
    }

    /**
     * Returns all container definitions.
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface[]
     */
    public function getContainerDefinitions()
    {
        return $this->containerDefinitions;
    }
}
