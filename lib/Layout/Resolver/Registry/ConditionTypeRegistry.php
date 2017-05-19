<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;

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
     * @throws \Netgen\BlockManager\Exception\Layout\ConditionTypeException If condition type does not exist
     *
     * @return \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface
     */
    public function getConditionType($type)
    {
        if (!$this->hasConditionType($type)) {
            throw ConditionTypeException::noConditionType($type);
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

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->conditionTypes);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->conditionTypes);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasConditionType($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getConditionType($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
