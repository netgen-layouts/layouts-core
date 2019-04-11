<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use ArrayIterator;
use Netgen\Layouts\Block\BlockType\BlockType;
use Netgen\Layouts\Exception\Block\BlockTypeException;
use Netgen\Layouts\Exception\RuntimeException;
use Traversable;

final class BlockTypeRegistry implements BlockTypeRegistryInterface
{
    /**
     * @var \Netgen\Layouts\Block\BlockType\BlockType[]
     */
    private $blockTypes;

    /**
     * @param \Netgen\Layouts\Block\BlockType\BlockType[] $blockTypes
     */
    public function __construct(array $blockTypes)
    {
        $this->blockTypes = array_filter(
            $blockTypes,
            static function (BlockType $blockType): bool {
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
            static function (BlockType $blockType): bool {
                return $blockType->isEnabled();
            }
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->blockTypes);
    }

    public function count(): int
    {
        return count($this->blockTypes);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->hasBlockType($offset);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getBlockType($offset);
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
