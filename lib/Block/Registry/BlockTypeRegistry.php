<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Exception\Block\BlockTypeException;

class BlockTypeRegistry implements BlockTypeRegistryInterface
{
    /**
     * @var array
     */
    protected $blockTypes = array();

    /**
     * @var array
     */
    protected $blockTypeGroups = array();

    /**
     * Adds a block type to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockType\BlockType $blockType
     */
    public function addBlockType($identifier, BlockType $blockType)
    {
        $this->blockTypes[$identifier] = $blockType;
    }

    /**
     * Returns if registry has a block type.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockType($identifier)
    {
        return isset($this->blockTypes[$identifier]);
    }

    /**
     * Returns the block type with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockTypeException If block type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType
     */
    public function getBlockType($identifier)
    {
        if (!$this->hasBlockType($identifier)) {
            throw BlockTypeException::noBlockType($identifier);
        }

        return $this->blockTypes[$identifier];
    }

    /**
     * Returns all block types.
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    public function getBlockTypes()
    {
        return $this->blockTypes;
    }

    /**
     * Adds a block type group to registry.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Block\BlockType\BlockTypeGroup $blockTypeGroup
     */
    public function addBlockTypeGroup($identifier, BlockTypeGroup $blockTypeGroup)
    {
        $this->blockTypeGroups[$identifier] = $blockTypeGroup;
    }

    /**
     * Returns if registry has a block type group.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function hasBlockTypeGroup($identifier)
    {
        return isset($this->blockTypeGroups[$identifier]);
    }

    /**
     * Returns the block type group with provided identifier.
     *
     * @param string $identifier
     *
     * @throws \Netgen\BlockManager\Exception\Block\BlockTypeException If block type group with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockTypeGroup
     */
    public function getBlockTypeGroup($identifier)
    {
        if (!$this->hasBlockTypeGroup($identifier)) {
            throw BlockTypeException::noBlockTypeGroup($identifier);
        }

        return $this->blockTypeGroups[$identifier];
    }

    /**
     * Returns all block type groups.
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockTypeGroup[]
     */
    public function getBlockTypeGroups()
    {
        return $this->blockTypeGroups;
    }
}
