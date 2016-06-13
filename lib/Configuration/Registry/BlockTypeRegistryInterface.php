<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;

interface BlockTypeRegistryInterface
{
    /**
     * Adds a block type to registry.
     *
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockType $blockType
     */
    public function addBlockType(BlockType $blockType);

    /**
     * Returns if registry has a block type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockType($identifier);

    /**
     * Returns the block type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If block type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    public function getBlockType($identifier);

    /**
     * Returns all block types.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType[]
     */
    public function getBlockTypes();

    /**
     * Adds a block type group to registry.
     *
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup $blockTypeGroup
     */
    public function addBlockTypeGroup(BlockTypeGroup $blockTypeGroup);

    /**
     * Returns if registry has a block type group.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockTypeGroup($identifier);

    /**
     * Returns the block type group with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If block type group with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup
     */
    public function getBlockTypeGroup($identifier);

    /**
     * Returns all block type groups.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup[]
     */
    public function getBlockTypeGroups();
}
