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
    public function invalidateLayouts(array $layoutIds): void;

    /**
     * Invalidates all layouts.
     */
    public function invalidateAllLayouts(): void;

    /**
     * Invalidates all provided blocks.
     *
     * @param int[]|string[] $blockIds
     */
    public function invalidateBlocks(array $blockIds): void;

    /**
     * Invalidates all blocks from provided layouts.
     *
     * @param int[]|string[] $layoutIds
     */
    public function invalidateLayoutBlocks(array $layoutIds): void;

    /**
     * Invalidates all blocks.
     */
    public function invalidateAllBlocks(): void;

    /**
     * Commits the cache clear operations to the backend.
     */
    public function commit(): bool;
}
