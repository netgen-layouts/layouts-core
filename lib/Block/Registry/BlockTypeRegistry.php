<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

use function array_filter;
use function count;

/**
 * @implements \IteratorAggregate<string, \Netgen\Layouts\Block\BlockType\BlockType>
 * @implements \ArrayAccess<string, \Netgen\Layouts\Block\BlockType\BlockType>
 */
final class BlockTypeRegistry implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array<string, \Netgen\Layouts\Block\BlockType\BlockType>
     */
    private array $blockTypes;

    /**
     * @param array<string, \Netgen\Layouts\Block\BlockType\BlockType> $blockTypes
     */
    public function __construct(array $blockTypes)
    {
        $this->blockTypes = array_filter(
            $blockTypes,
            static fn (BlockType $blockType): bool => true,
        );
    }

    /**
     * Returns if registry has a block type.
     */
    public function hasBlockType(string $identifier): bool
    {
        return isset($this->blockTypes[$identifier]);
    }

    /**
     * Returns the block type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockTypeException If block type with provided identifier does not exist
     */
    public function getBlockType(string $identifier): BlockType
    {
        if (!$this->hasBlockType($identifier)) {
            throw BlockTypeException::noBlockType($identifier);
        }

        return $this->blockTypes[$identifier];
    }

    /**
     * Returns all block types.
     *
     * @return array<string, \Netgen\Layouts\Block\BlockType\BlockType>
     */
    public function getBlockTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->blockTypes;
        }

        return array_filter(
            $this->blockTypes,
            static fn (BlockType $blockType): bool => $blockType->isEnabled(),
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->blockTypes);
    }

    public function count(): int
    {
        return count($this->blockTypes);
    }

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return $this->hasBlockType($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): BlockType
    {
        return $this->getBlockType($offset);
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
