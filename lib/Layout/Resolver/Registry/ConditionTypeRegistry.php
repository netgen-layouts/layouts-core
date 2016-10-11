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
        $this->conditionTypes[$conditionType->getType()] = $conditionType;
    }

    /**
     * Returns if registry has a condition type.
     *
     * @param string $type
     *
     * @return bool
     */
    public function hasConditionType($type)
    {
        return isset($this->conditionTypes[$type]);
    }

    /**
     * Returns a condition type with provided type.
     *
     * @param string $type
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If condition type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    public function getConditionType($type)
    {
        if (!$this->hasConditionType($type)) {
            throw new InvalidArgumentException(
                'type',
                sprintf(
                    'Condition type "%s" does not exist.',
                    $type
                )
            );
        }

        return $this->conditionTypes[$type];
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
