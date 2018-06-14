<?php

declare(strict_types=1);

namespace Netgen\BlockManager\HttpCache\Block;

use Netgen\BlockManager\API\Values\Block\Block;

interface CacheableResolverInterface
{
    /**
     * Returns if the block is cacheable by HTTP caches.
     */
    public function isCacheable(Block $block): bool;
}
