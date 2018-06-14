<?php

declare(strict_types=1);

namespace Netgen\BlockManager\HttpCache;

final class NullClient implements ClientInterface
{
    public function invalidateLayouts(array $layoutIds): void
    {
    }

    public function invalidateAllLayouts(): void
    {
    }

    public function invalidateBlocks(array $blockIds): void
    {
    }

    public function invalidateLayoutBlocks(array $layoutIds): void
    {
    }

    public function invalidateAllBlocks(): void
    {
    }

    public function commit(): bool
    {
        return true;
    }
}
