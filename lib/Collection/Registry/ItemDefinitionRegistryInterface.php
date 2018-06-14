<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Collection\Item\ItemDefinitionInterface;

interface ItemDefinitionRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a item definition to registry.
     */
    public function addItemDefinition(string $valueType, ItemDefinitionInterface $itemDefinition): void;

    /**
     * Returns if registry has a item definition.
     */
    public function hasItemDefinition(string $valueType): bool;

    /**
     * Returns a item definition with provided value type.
     *
     * @throws \Netgen\BlockManager\Exception\Collection\ItemDefinitionException If item definition does not exist
     */
    public function getItemDefinition(string $valueType): ItemDefinitionInterface;

    /**
     * Returns all item definitions.
     *
     * @return \Netgen\BlockManager\Collection\Item\ItemDefinitionInterface[]
     */
    public function getItemDefinitions(): array;
}
