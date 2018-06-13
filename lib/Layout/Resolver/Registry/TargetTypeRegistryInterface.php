<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;

interface TargetTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
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
