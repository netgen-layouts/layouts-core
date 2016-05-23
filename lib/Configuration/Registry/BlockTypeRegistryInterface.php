<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;

interface BlockTypeRegistryInterface
{
    /**
     * Adds a block type.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockType $blockType
     */
    public function addBlockType($identifier, BlockType $blockType);

    /**
     * Returns if block type exists in the registry.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockType($identifier);

    /**
     * Returns the block type.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If block type with provided identifier does not exist.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    public function getBlockType($identifier);

    /**
     * Returns all block types.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType[]
     */
    public function allBlockTypes();

    /**
     * Adds a block type group.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup $blockTypeGroup
     */
    public function addBlockTypeGroup($identifier, BlockTypeGroup $blockTypeGroup);

    /**
     * Returns if block type group exists in the registry.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockTypeGroup($identifier);

    /**
     * Returns the block type group.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If block type group with provided identifier does not exist.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup
     */
    public function getBlockTypeGroup($identifier);

    /**
     * Returns all block type groups.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup[]
     */
    public function allBlockTypeGroups();
}
