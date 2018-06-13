<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Exception\RuntimeException;

final class BlockTypeGroupRegistry implements BlockTypeGroupRegistryInterface
{
    /**
     * @var array
     */
    private $blockTypeGroups = [];

    public function addBlockTypeGroup($identifier, BlockTypeGroup $blockTypeGroup)
    {
        $this->blockTypeGroups[$identifier] = $blockTypeGroup;
    }

    public function hasBlockTypeGroup($identifier)
    {
        return isset($this->blockTypeGroups[$identifier]);
    }

    public function getBlockTypeGroup($identifier)
    {
        if (!$this->hasBlockTypeGroup($identifier)) {
            throw BlockTypeException::noBlockTypeGroup($identifier);
        }

        return $this->blockTypeGroups[$identifier];
    }

    public function getBlockTypeGroups($onlyEnabled = false)
    {
        if (!$onlyEnabled) {
            return $this->blockTypeGroups;
        }

        return array_filter(
            $this->blockTypeGroups,
            function (BlockTypeGroup $blockTypeGroup) {
                return $blockTypeGroup->isEnabled();
            }
        );
    }

    public function getIterator()
    {
        return new ArrayIterator($this->blockTypeGroups);
    }

    public function count()
    {
        return count($this->blockTypeGroups);
    }

    public function offsetExists($offset)
    {
        return $this->hasBlockTypeGroup($offset);
    }

    public function offsetGet($offset)
    {
        return $this->getBlockTypeGroup($offset);
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
