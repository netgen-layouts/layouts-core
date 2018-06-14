<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;

interface BlockTypeGroupRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Adds a block type group to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockType\BlockTypeGroup $blockTypeGroup
     */
    public function addBlockTypeGroup(string $identifier, BlockTypeGroup $blockTypeGroup): void;

    /**
     * Returns if registry has a block type group.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockTypeGroup(string $identifier): bool;

    /**
     * Returns the block type group with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockTypeException If block type group with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    public function getBlockTypeGroup(string $identifier): BlockTypeGroup;

    /**
     * Returns all block type groups.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockTypeGroup[]
     */
    public function getBlockTypeGroups(bool $onlyEnabled = false): array;
}
