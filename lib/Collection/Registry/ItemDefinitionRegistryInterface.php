<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;

interface ItemDefinitionRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a item definition.
     */
    public function hasItemDefinition(string $valueType): bool;

    /**
     * Returns a item definition with provided value type.
     *
     * @throws \Netgen\Layouts\Exception\Collection\ItemDefinitionException If item definition does not exist
     */
    public function getItemDefinition(string $valueType): ItemDefinitionInterface;

    /**
     * Returns all item definitions.
     *
     * @return \Netgen\Layouts\Collection\Item\ItemDefinitionInterface[]
     */
    public function getItemDefinitions(): array;
}
