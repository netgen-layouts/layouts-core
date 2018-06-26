<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Block\Registry;

use ArrayIterator;
use Netgen\BlockManager\Block\BlockType\BlockType;
use Netgen\BlockManager\Exception\Block\BlockTypeException;
use Netgen\BlockManager\Exception\RuntimeException;

final class BlockTypeRegistry implements BlockTypeRegistryInterface
{
    /**
     * @var \Netgen\BlockManager\Block\BlockType\BlockType[]
     */
    private $blockTypes = [];

    /**
     * @param \Netgen\BlockManager\Block\BlockType\BlockType[] $blockTypes
     */
    public function __construct(array $blockTypes)
    {
        $this->blockTypes = array_filter(
            $blockTypes,
            function (BlockType $blockType): bool {
                return true;
            }
        );
    }

    public function hasBlockType(string $identifier): bool
    {
        return isset($this->blockTypes[$identifier]);
    }

    public function getBlockType(string $identifier): BlockType
    {
        if (!$this->hasBlockType($identifier)) {
            throw BlockTypeException::noBlockType($identifier);
        }

        return $this->blockTypes[$identifier];
    }

    public function getBlockTypes(bool $onlyEnabled = false): array
    {
        if (!$onlyEnabled) {
            return $this->blockTypes;
        }

        return array_filter(
            $this->blockTypes,
            function (BlockType $blockType): bool {
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
