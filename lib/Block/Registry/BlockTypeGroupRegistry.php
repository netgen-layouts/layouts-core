<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Exception\Block\BlockTypeException;

class BlockTypeGroupRegistry implements BlockTypeGroupRegistryInterface
{
    /**
     * @var array
     */
    protected $blockTypeGroups = array();

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
