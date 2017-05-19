<?php

namespace Netgen\BlockManager\Block\Registry;

use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Exception\Block\BlockTypeException;

class BlockTypeRegistry implements BlockTypeRegistryInterface
{
    /**
     * @var array
     */
    protected $blockTypes = array();

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
}
