<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Block;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Exception\RuntimeException;
use Netgen\Layouts\Utils\HydratorTrait;
use Traversable;

/**
 * Placeholder represents a set of blocks inside a container block.
 *
 * Each container block can have multiple placeholders, allowing to render
 * each block set separately.
 *
 * @implements \IteratorAggregate<int, \Netgen\Layouts\API\Values\Block\Block>
 * @implements \ArrayAccess<int, \Netgen\Layouts\API\Values\Block\Block>
 */
final class Placeholder implements ArrayAccess, IteratorAggregate, Countable
{
    use HydratorTrait;

    /**
     * Returns the placeholder identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns all blocks in this placeholder.
     */
    public private(set) BlockList $blocks {
        get => BlockList::fromArray($this->blocks->toArray());
    }

    public function getIterator(): Traversable
    {
        return $this->blocks->getIterator();
    }

    public function count(): int
    {
        return $this->blocks->count();
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->blocks->offsetExists($offset);
    }

    public function offsetGet(mixed $offset): ?Block
    {
        return $this->blocks->offsetGet($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new RuntimeException('Method call not supported.');
    }
}
