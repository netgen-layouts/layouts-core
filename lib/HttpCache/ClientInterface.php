<?php

declare(strict_types=1);

namespace Netgen\BlockManager\HttpCache;

interface ClientInterface
{
    /**
     * Invalidates all provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayouts(array $layoutIds);

    /**
     * Invalidates all layouts.
     */
    public function invalidateAllLayouts();

    /**
     * Invalidates all provided blocks.
     *
     * @param int[]|string[] $blockIds
     */
    public function invalidateBlocks(array $blockIds);

    /**
     * Invalidates all blocks from provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayoutBlocks(array $layoutIds);

    /**
     * Invalidates all blocks.
     */
    public function invalidateAllBlocks();

    /**
     * Commits the cache clear operations to the backend.
     *
     * @return bool
     */
    public function commit();
}
