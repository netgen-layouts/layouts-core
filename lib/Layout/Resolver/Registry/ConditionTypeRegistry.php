<?php

namespace Netgen\BlockManager\Layout\Resolver\Registry;

use ArrayIterator;
use Netgen\BlockManager\Exception\Layout\ConditionTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface;

final class ConditionTypeRegistry implements ConditionTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Layout\Resolver\ConditionTypeInterface[]
     */
    private $conditionTypes = array();

    public function addConditionType(ConditionTypeInterface $conditionType)
    {
        $this->conditionTypes[$conditionType->getType()] = $conditionType;
    }

    public function hasConditionType($type)
    {
        return isset($this->conditionTypes[$type]);
    }

    public function getConditionType($type)
    {
        if (!$this->hasConditionType($type)) {
            throw ConditionTypeException::noConditionType($type);
        }

        return $this->conditionTypes[$type];
    }

    public function getConditionTypes()
    {
        return $this->conditionTypes;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->conditionTypes);
    }

    public function count()
    {
        return count($this->conditionTypes);
    }

    public function offsetExists($offset)
    {
        return $this->hasConditionType($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getConditionType($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
