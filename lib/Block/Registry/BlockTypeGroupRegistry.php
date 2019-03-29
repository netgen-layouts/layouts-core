<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockType\BlockTypeGroup;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Exception\RuntimeException;
use Traversable;

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

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->blockTypeGroups);
    }

    public function count(): int
    {
        return count($this->blockTypeGroups);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasBlockTypeGroup($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getBlockTypeGroup($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new RuntimeException('Method call not supported.');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new RuntimeException('Method call not supported.');
    }
}
