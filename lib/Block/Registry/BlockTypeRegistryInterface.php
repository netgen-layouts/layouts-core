<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Block\BlockType\BlockType;

interface BlockTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a block type to registry.
     */
    public function addBlockType(string $identifier, BlockType $blockType): void;

    /**
     * Returns if registry has a block type.
     */
    public function hasBlockType(string $identifier): bool;

    /**
     * Returns the block type with provided identifier.
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockTypeException If block type with provided identifier does not exist
     */
    public function getBlockType(string $identifier): BlockType;

    /**
     * Returns all block types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    public function getBlockTypes(bool $onlyEnabled = false): array;
}
