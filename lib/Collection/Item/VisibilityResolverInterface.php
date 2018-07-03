<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;

interface VisibilityResolverInterface
{
    /**
     * Returns if the provided collection item is visible.
     */
    public function isVisible(Item $item): bool;
}
