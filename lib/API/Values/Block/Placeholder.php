<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Values\Block;

use ArrayAccess;
use Countable;
use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;
use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Utils\HydratorTrait;
use Traversable;

/**
 * Placeholder represents a set of blocks inside a container block.
 *
 * Each container block can have multiple placeholders, allowing to render
 * each block set separately.
 */
final class Placeholder implements ArrayAccess, IteratorAggregate, Countable
{
    use HydratorTrait;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $blocks;

    public function __construct()
    {
        $this->blocks = $this->blocks ?? new ArrayCollection();
    }

    /**
     * Returns the placeholder identifier.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns all blocks in this placeholder.
     */
    public function getBlocks(): BlockList
    {
        return new BlockList($this->blocks->toArray());
    }

    public function getIterator(): Traversable
    {
        return $this->blocks->getIterator();
    }

    public function count(): int
    {
        return $this->blocks->count();
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->blocks->offsetExists($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->blocks->offsetGet($offset);
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
