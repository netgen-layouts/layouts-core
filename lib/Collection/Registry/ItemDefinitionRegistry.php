<?php

namespace Netgen\BlockManager\Collection\Registry;

use ArrayIterator;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;
use Netgen\BlockManager\Exception\Collection\ItemDefinitionException;
use Netgen\BlockManager\Exception\RuntimeException;

final class ItemDefinitionRegistry implements ItemDefinitionRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface[]
     */
    private $itemDefinitions = array();

    public function addItemDefinition($valueType, ItemDefinitionInterface $itemDefinition)
    {
        $this->itemDefinitions[$valueType] = $itemDefinition;
    }

    public function hasItemDefinition($valueType)
    {
        return isset($this->itemDefinitions[$valueType]);
    }

    public function getItemDefinition($valueType)
    {
        if (!$this->hasItemDefinition($valueType)) {
            throw ItemDefinitionException::noItemDefinition($valueType);
        }

        return $this->itemDefinitions[$valueType];
    }

    public function getItemDefinitions()
    {
        return $this->itemDefinitions;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->itemDefinitions);
    }

    public function count()
    {
        return count($this->itemDefinitions);
    }

    public function offsetExists($offset)
    {
        return $this->hasItemDefinition($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getItemDefinition($offset);
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
