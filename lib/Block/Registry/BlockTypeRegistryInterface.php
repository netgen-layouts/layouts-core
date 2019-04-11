<?php

declare(strict_types=1);

namespace Netgen\Layouts\Block\Registry;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Netgen\Layouts\Block\BlockType\BlockType;

interface BlockTypeRegistryInterface extends IteratorAggregate, Countable, ArrayAccess
{
    /**
     * Returns if registry has a block type.
     */
    public function hasBlockType(string $identifier): bool;

    /**
     * Returns the block type with provided identifier.
     *
     * @throws \Netgen\Layouts\Exception\Block\BlockTypeException If block type with provided identifier does not exist
     */
    public function getBlockType(string $identifier): BlockType;

    /**
     * Returns all block types.
     *
     * @param bool $onlyEnabled
     *
     * @return \Netgen\Layouts\Block\BlockType\BlockType[]
     */
    public function getBlockTypes(bool $onlyEnabled = false): array;
}
