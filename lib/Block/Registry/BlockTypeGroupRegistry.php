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
     * @var \Netgen\BlockManager\Block\BlockType\BlockTypeGroup[]
     */
    private $blockTypeGroups;

    /**
     * @param \Netgen\BlockManager\Block\BlockType\BlockTypeGroup[] $blockTypeGroups
     */
    public function __construct(array $blockTypeGroups)
    {
        $this->blockTypeGroups = array_filter(
            $blockTypeGroups,
            function (BlockTypeGroup $blockTypeGroup): bool {
                return true;
            }
        );
    }

    public function hasBlockTypeGroup(string $identifier): bool
    {
        return isset($this->blockTypeGroups[$identifier]);
    }

    public function getBlockTypeGroup(string $identifier): BlockTypeGroup
    {
        if (!$this->hasBlockTypeGroup($identifier)) {
            throw BlockTypeException::noBlockTypeGroup($identifier);
        }

        return $this->blockTypeGroups[$identifier];
    }

    public function getBlockTypeGroups(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->blockTypeGroups;
        }

        return array_filter(
            $this->blockTypeGroups,
            function (BlockTypeGroup $blockTypeGroup): bool {
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
