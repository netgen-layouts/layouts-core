<?php

namespace Netgen\BlockManager\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Exception\RuntimeException;

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
     * @param bool $onlyEnabled
     *
     * @return \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    public function getBlockTypes($onlyEnabled = false)
    {
        if (!$onlyEnabled) {
            return $this->blockTypes;
        }

        return array_filter(
            $this->blockTypes,
            function (BlockType $blockType) {
                return $blockType->isEnabled();
            }
        );
    }

    /**
     * Retrieve an external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->blockTypes);
    }

    /**
     * Count elements of an object.
     *
     * @return int
     */
    public function count()
    {
        return count($this->blockTypes);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasBlockType($offset);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getBlockType($offset);
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
