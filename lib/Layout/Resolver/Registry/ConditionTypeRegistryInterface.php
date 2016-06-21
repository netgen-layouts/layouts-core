<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;

interface ConditionTypeRegistryInterface
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
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConditionType($identifier);

    /**
     * Returns a condition type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If condition type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    public function getConditionType($identifier);

    /**
     * Returns all condition types.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface[]
     */
    public function getConditionTypes();
}
