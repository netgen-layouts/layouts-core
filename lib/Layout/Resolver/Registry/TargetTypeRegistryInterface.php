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
     * @param string $type
     *
     * @return bool
     */
    public function hasTargetType($type);

    /**
     * Returns a target type with provided type.
     *
     * @param string $type
     *
     * @throws \Netgen\BlockManager\Exception\Layout\TargetTypeException If target type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface
     */
    public function getTargetType($type);

    /**
     * Returns all target types.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface[]
     */
    public function getTargetTypes();
}
