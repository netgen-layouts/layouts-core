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
     * Returns if registry has a target type.
     */
    public function hasTargetType(string $type): bool;

    /**
     * Returns a target type with provided type.
     *
     * @throws \Netgen\BlockManager\Exception\Layout\TargetTypeException If target type does not exist
     */
    public function getTargetType(string $type): TargetTypeInterface;

    /**
     * Returns all target types.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\TargetTypeInterface[]
     */
    public function getTargetTypes(): array;
}
