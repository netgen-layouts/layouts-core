<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;
use Netgen\Layouts\Exception\Collection\ItemDefinitionException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

use function array_filter;
use function count;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Collection\Item\ItemDefinitionInterface>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Collection\Item\ItemDefinitionInterface>
 */
final class ItemDefinitionRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Collection\Item\ItemDefinitionInterface>
     */
    private array $itemDefinitions;

    /**
     * @param array<string, \Netgen\Layouts\Collection\Item\ItemDefinitionInterface> $itemDefinitions
     */
    public function __construct(array $itemDefinitions)
    {
        $this->itemDefinitions = array_filter(
            $itemDefinitions,
            static fn (ItemDefinitionInterface $itemDefinition): bool => true,
        );
    }

    /**
     * Returns if registry has a item definition.
     */
    public function hasItemDefinition(string $valueType): bool
    {
        return isset($this->itemDefinitions[$valueType]);
    }

    /**
     * Returns a item definition with provided value type.
     *
     * @throws \Netgen\Layouts\Exception\Collection\ItemDefinitionException If item definition does not exist
     */
    public function getItemDefinition(string $valueType): ItemDefinitionInterface
    {
        if (!$this->hasItemDefinition($valueType)) {
            throw ItemDefinitionException::noItemDefinition($valueType);
        }

        return $this->itemDefinitions[$valueType];
    }

    /**
     * Returns all item definitions.
     *
     * @return array<string, \Netgen\Layouts\Collection\Item\ItemDefinitionInterface>
     */
    public function getItemDefinitions(): array
    {
        return $this->itemDefinitions;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->itemDefinitions);
    }

    public function count(): int
    {
        return count($this->itemDefinitions);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasItemDefinition($offset);
    }

    public function offsetGet(mixed $offset): ItemDefinitionInterface
    {
        return $this->getItemDefinition($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
