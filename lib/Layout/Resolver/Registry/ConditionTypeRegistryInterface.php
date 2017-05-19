<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;

interface ConditionTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a condition type to registry.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType
     */
    public function addConditionType(ConditionTypeInterface $conditionType);

    /**
     * Returns if registry has a condition type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasConditionType($type);

    /**
     * Returns a condition type with provided type.
     *
     * @param string $type
     *
     * @throws \Netgen\BlockManager\Exception\Layout\ConditionTypeException If condition type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    public function getConditionType($type);

    /**
     * Returns all condition types.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface[]
     */
    public function getConditionTypes();
}
