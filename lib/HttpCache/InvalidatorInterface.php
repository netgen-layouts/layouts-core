<?php

declare(strict_types=1);

namespace Netgen\Layouts\HttpCache;

interface InvalidatorInterface
{
    /**
     * Invalidates all provided layouts.
     *
     * @param string[] $layoutIds
     */
    public function invalidateLayouts(array $layoutIds): void;

    /**
     * Invalidates all provided blocks.
     *
     * @param string[] $blockIds
     */
    public function invalidateBlocks(array $blockIds): void;

    /**
     * Invalidates all blocks from provided layouts.
     *
     * @param string[] $layoutIds
     */
    public function invalidateLayoutBlocks(array $layoutIds): void;

    /**
     * Commits the cache clear operations to the backend.
     */
    public function commit(): bool;
}
