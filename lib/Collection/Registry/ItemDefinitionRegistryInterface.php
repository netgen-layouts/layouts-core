<?php

namespace Netgen\BlockManager\Collection\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;

interface ItemDefinitionRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a item definition to registry.
     *
     * @param string $valueType
     * @param \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface $itemDefinition
     */
    public function addItemDefinition($valueType, ItemDefinitionInterface $itemDefinition);

    /**
     * Returns if registry has a item definition.
     *
     * @param string $valueType
     *
     * @return bool
     */
    public function hasItemDefinition($valueType);

    /**
     * Returns a item definition with provided value type.
     *
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Collection\ItemDefinitionException If item definition does not exist
     *
     * @return \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface
     */
    public function getItemDefinition($valueType);

    /**
     * Returns all item definitions.
     *
     * @return \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface[]
     */
    public function getItemDefinitions();
}
