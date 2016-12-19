<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\ContainerType\ContainerType;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class ContainerTypeRegistry implements ContainerTypeRegistryInterface
{
    /**
     * @var array
     */
    protected $containerTypes = array();

    /**
     * Adds a container type to registry.
     *
     * @param \Netgen\BlockManager\Configuration\ContainerType\ContainerType $containerType
     */
    public function addContainerType(ContainerType $containerType)
    {
        $this->containerTypes[$containerType->getIdentifier()] = $containerType;
    }

    /**
     * Returns if registry has a container type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasContainerType($identifier)
    {
        return isset($this->containerTypes[$identifier]);
    }

    /**
     * Returns the container type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If container type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Configuration\ContainerType\ContainerType
     */
    public function getContainerType($identifier)
    {
        if (!$this->hasContainerType($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Container type with "%s" identifier does not exist.',
                    $identifier
                )
            );
        }

        return $this->containerTypes[$identifier];
    }

    /**
     * Returns all container types.
     *
     * @return \Netgen\BlockManager\Configuration\ContainerType\ContainerType[]
     */
    public function getContainerTypes()
    {
        return $this->containerTypes;
    }
}
