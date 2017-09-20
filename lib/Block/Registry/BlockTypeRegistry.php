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
    private $blockTypes = array();

    public function addBlockType($identifier, BlockType $blockType)
    {
        $this->blockTypes[$identifier] = $blockType;
    }

    public function hasBlockType($identifier)
    {
        return isset($this->blockTypes[$identifier]);
    }

    public function getBlockType($identifier)
    {
        if (!$this->hasBlockType($identifier)) {
            throw BlockTypeException::noBlockType($identifier);
        }

        return $this->blockTypes[$identifier];
    }

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

    public function getIterator()
    {
        return new ArrayIterator($this->blockTypes);
    }

    public function count()
    {
        return count($this->blockTypes);
    }

    public function offsetExists($offset)
    {
        return $this->hasBlockType($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getBlockType($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new RuntimeException('Method call not supported.');
    }

    public function offsetUnset($offset)
    {
        throw new RuntimeException('Method call not supported.');
    }
}
