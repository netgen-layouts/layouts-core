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
use function count;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Block\BlockType\BlockTypeGroup>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Block\BlockType\BlockTypeGroup>
 */
final class BlockTypeGroupRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Block\BlockType\BlockTypeGroup>
     */
    private array $blockTypeGroups;

    /**
     * @param array<string, \Netgen\Layouts\Block\BlockType\BlockTypeGroup> $blockTypeGroups
     */
    public function __construct(array $blockTypeGroups)
    {
        $this->blockTypeGroups = array_filter(
            $blockTypeGroups,
            static fn (BlockTypeGroup $blockTypeGroup): bool => true,
        );
    }

    /**
     * Returns if registry has a block type group.
     */
    public function hasBlockTypeGroup(string $identifier): bool
    {
        return isset($this->blockTypeGroups[$identifier]);
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
            static fn (BlockTypeGroup $blockTypeGroup): bool => $blockTypeGroup->isEnabled(),
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

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->hasBlockTypeGroup($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): BlockTypeGroup
    {
        return $this->getBlockTypeGroup($offset);
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
