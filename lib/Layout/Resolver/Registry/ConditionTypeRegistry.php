<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;
use Netgen\BlockManager\Exception\InvalidArgumentException;

class ConditionTypeRegistry implements ConditionTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface[]
     */
    protected $conditionTypes = array();

    /**
     * Adds a condition type to registry.
     *
     * @param \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface $conditionType
     */
    public function addConditionType(ConditionTypeInterface $conditionType)
    {
        $this->conditionTypes[$conditionType->getIdentifier()] = $conditionType;
    }

    /**
     * Returns if registry has a condition type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasConditionType($identifier)
    {
        return isset($this->conditionTypes[$identifier]);
    }

    /**
     * Returns a condition type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If condition type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    public function getConditionType($identifier)
    {
        if (!$this->hasConditionType($identifier)) {
            throw new InvalidArgumentException('condition type', $identifier);
        }

        return $this->conditionTypes[$identifier];
    }

    /**
     * Returns all condition types.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface[]
     */
    public function getConditionTypes()
    {
        return $this->conditionTypes;
    }
}
