<?php

namespace Netgen\BlockManager\Configuration\Registry;

use Netgen\BlockManager\Configuration\BlockType\BlockType;
use Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Exception\InvalidArgumentException;

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
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockType $blockType
     */
    public function addBlockType(BlockType $blockType)
    {
        $this->blockTypes[$blockType->getIdentifier()] = $blockType;
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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If block type with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType
     */
    public function getBlockType($identifier)
    {
        if (!$this->hasBlockType($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Block type with "%s" identifier does not exist.',
                    $identifier
                )
            );
        }

        return $this->blockTypes[$identifier];
    }

    /**
     * Returns all block types.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockType[]
     */
    public function getBlockTypes()
    {
        return $this->blockTypes;
    }

    /**
     * Adds a block type group to registry.
     *
     * @param \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup $blockTypeGroup
     */
    public function addBlockTypeGroup(BlockTypeGroup $blockTypeGroup)
    {
        $this->blockTypeGroups[$blockTypeGroup->getIdentifier()] = $blockTypeGroup;
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
     * @throws \Netgen\BlockManager\Exception\InvalidArgumentException If block type group with provided identifier does not exist
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup
     */
    public function getBlockTypeGroup($identifier)
    {
        if (!$this->hasBlockTypeGroup($identifier)) {
            throw new InvalidArgumentException(
                'identifier',
                sprintf(
                    'Block type group with "%s" identifier does not exist.',
                    $identifier
                )
            );
        }

        return $this->blockTypeGroups[$identifier];
    }

    /**
     * Returns all block type groups.
     *
     * @return \Netgen\BlockManager\Configuration\BlockType\BlockTypeGroup[]
     */
    public function getBlockTypeGroups()
    {
        return $this->blockTypeGroups;
    }
}
