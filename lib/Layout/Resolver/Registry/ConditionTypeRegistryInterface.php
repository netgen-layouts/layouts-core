<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;

interface ConditionTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a condition type.
     */
    public function hasConditionType(string $type): bool;

    /**
     * Returns a condition type with provided type.
     *
     * @throws \Netgen\BlockManager\Exception\Layout\ConditionTypeException If condition type does not exist
     */
    public function getConditionType(string $type): ConditionTypeInterface;

    /**
     * Returns all condition types.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface[]
     */
    public function getConditionTypes(): array;
}
