<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;

interface BlockTypeRegistryInterface
{
    /**
     * Adds a block type to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockType\BlockType $blockType
     */
    public function addBlockType($identifier, BlockType $blockType);

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
     * @return \Netgen\BlockManager\Block\BlockType\BlockType
     */
    public function getBlockType($identifier);

    /**
     * Returns all block types.
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    public function getBlockTypes();

    /**
     * Adds a block type group to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockType\BlockTypeGroup $blockTypeGroup
     */
    public function addBlockTypeGroup($identifier, BlockTypeGroup $blockTypeGroup);

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
     * @return \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    public function getBlockTypeGroup($identifier);

    /**
     * Returns all block type groups.
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockTypeGroup[]
     */
    public function getBlockTypeGroups();
}
