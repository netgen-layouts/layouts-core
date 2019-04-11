<?php

declare(strict_types=1);

namespace Netgen\Layouts\Collection\Item;

use Netgen\Layouts\API\Values\Collection\Item;

interface VisibilityResolverInterface
{
    /**
     * Returns if the provided collection item is visible.
     */
    public function isVisible(Item $item): bool;
}
