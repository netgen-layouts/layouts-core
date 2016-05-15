<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use RuntimeException;

class BlockTypeRegistry
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
     * Adds a block type.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockType $blockType
     */
    public function addBlockType($identifier, BlockType $blockType)
    {
        $this->blockTypes[$identifier] = $blockType;
    }

    /**
     * Returns if block type exists in the registry.
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
     * Returns the block type.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If block type with provided identifier does not exist.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    public function getBlockType($identifier)
    {
        if (!$this->hasBlockType($identifier)) {
            throw new RuntimeException(sprintf('Block type "%s" does not exist.', $identifier));
        }

        return $this->blockTypes[$identifier];
    }

    /**
     * Returns all block types.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType[]
     */
    public function allBlockTypes()
    {
        return $this->blockTypes;
    }

    /**
     * Adds a block type group.
     *
     * @param string $identifier
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup $blockTypeGroup
     */
    public function addBlockTypeGroup($identifier, BlockTypeGroup $blockTypeGroup)
    {
        $this->blockTypeGroups[$identifier] = $blockTypeGroup;
    }

    /**
     * Returns if block type group exists in the registry.
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
     * Returns the block type group.
     *
     * @param string $identifier
     *
     * @throws \RuntimeException If block type group with provided identifier does not exist.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup
     */
    public function getBlockTypeGroup($identifier)
    {
        if (!$this->hasBlockTypeGroup($identifier)) {
            throw new RuntimeException(sprintf('Block type group "%s" does not exist.', $identifier));
        }

        return $this->blockTypeGroups[$identifier];
    }

    /**
     * Returns all block type groups.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup[]
     */
    public function allBlockTypeGroups()
    {
        return $this->blockTypeGroups;
    }
}
