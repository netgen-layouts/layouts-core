<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class TargetTypeRegistry implements TargetTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface[]
     */
    protected $targetTypes = array();

    /**
     * Adds a target type to registry.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType
     */
    public function addTargetType(TargetTypeInterface $targetType)
    {
        $this->targetTypes[$targetType->getIdentifier()] = $targetType;
    }

    /**
     * Returns if registry has a target type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasTargetType($identifier)
    {
        return isset($this->targetTypes[$identifier]);
    }

    /**
     * Returns a target type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If target type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    public function getTargetType($identifier)
    {
        if (!$this->hasTargetType($identifier)) {
            throw new InvalidArgumentException('target type', $identifier);
        }

        return $this->targetTypes[$identifier];
    }

    /**
     * Returns all target types.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface[]
     */
    public function getTargetTypes()
    {
        return $this->targetTypes;
    }
}
