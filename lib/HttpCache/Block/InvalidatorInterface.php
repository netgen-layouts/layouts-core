<?php

namespace Netgen\BlockManager\HttpCache\Block;

interface InvalidatorInterface
{
    /**
     * Invalidates all provided blocks.
     *
     * @param int[]|string[] $blockIds
     */
    public function invalidate(array $blockIds);

    /**
     * Invalidates all blocks in provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayoutBlocks(array $layoutIds);

    /**
     * Invalidates all blocks.
     */
    public function invalidateAll();
}
