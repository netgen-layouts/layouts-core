<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;

interface TargetTypeRegistryInterface
{
    /**
     * Adds a target type to registry.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface $targetType
     */
    public function addTargetType(TargetTypeInterface $targetType);

    /**
     * Returns if registry has a target type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasTargetType($identifier);

    /**
     * Returns a target type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If target type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    public function getTargetType($identifier);

    /**
     * Returns all target types.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface[]
     */
    public function getTargetTypes();
}
