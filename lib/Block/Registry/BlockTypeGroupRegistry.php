<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Block\BlockType\BlockTypeGroup;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

use function array_filter;
use function array_key_exists;
use function count;

/**
 * @implements \ArrayAccess<string, \Netgen\Layouts\Block\BlockType\BlockTypeGroup>
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Block\BlockType\BlockTypeGroup>
 */
final class BlockTypeGroupRegistry implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param array<string, \Netgen\Layouts\Block\BlockType\BlockTypeGroup> $blockTypeGroups
     */
    public function __construct(
        private array $blockTypeGroups,
    ) {
        $this->blockTypeGroups = array_filter(
            $this->blockTypeGroups,
            static fn (BlockTypeGroup $blockTypeGroup): bool => true,
        );
    }

    /**
     * Returns if registry has a block type group.
     */
    public function hasBlockTypeGroup(string $identifier): bool
    {
        return array_key_exists($identifier, $this->blockTypeGroups);
    }

    /**
     * Returns the block type group with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockTypeException If block type group with provided identifier does not exist
     */
    public function getBlockTypeGroup(string $identifier): BlockTypeGroup
    {
        if (!$this->hasBlockTypeGroup($identifier)) {
            throw BlockTypeException::noBlockTypeGroup($identifier);
        }

        return $this->blockTypeGroups[$identifier];
    }

    /**
     * Returns all block type groups.
     *
     * @return array<string, \Netgen\Layouts\Block\BlockType\BlockTypeGroup>
     */
    public function getBlockTypeGroups(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->blockTypeGroups;
        }

        return array_filter(
            $this->blockTypeGroups,
            static fn (BlockTypeGroup $blockTypeGroup): bool => $blockTypeGroup->isEnabled,
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->blockTypeGroups);
    }

    public function count(): int
    {
        return count($this->blockTypeGroups);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->hasBlockTypeGroup($offset);
    }

    public function offsetGet(mixed $offset): BlockTypeGroup
    {
        return $this->getBlockTypeGroup($offset);
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
