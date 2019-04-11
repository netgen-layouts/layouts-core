<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Registry;

use ArrayIterator;
use Netgen\Layouts\Collection\Item\ItemDefinitionInterface;
use Netgen\Layouts\Exception\Collection\ItemDefinitionException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

final class ItemDefinitionRegistry implements ItemDefinitionRegistryInterface
{
    /**
     * @var \Netgen\Layouts\Collection\Item\ItemDefinitionInterface[]
     */
    private $itemDefinitions;

    /**
     * @param \Netgen\Layouts\Collection\Item\ItemDefinitionInterface[] $itemDefinitions
     */
    public function __construct(array $itemDefinitions)
    {
        $this->itemDefinitions = array_filter(
            $itemDefinitions,
            static function (ItemDefinitionInterface $itemDefinition): bool {
                return true;
            }
        );
    }

    public function hasItemDefinition(string $valueType): bool
    {
        return isset($this->itemDefinitions[$valueType]);
    }

    public function getItemDefinition(string $valueType): ItemDefinitionInterface
    {
        if (!$this->hasItemDefinition($valueType)) {
            throw ItemDefinitionException::noItemDefinition($valueType);
        }

        return $this->itemDefinitions[$valueType];
    }

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

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasItemDefinition($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getItemDefinition($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
