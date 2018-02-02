<?php

namespace Netgen\BlockManager\Collection\Item;

use Netgen\BlockManager\API\Values\Collection\Item;

interface VisibilityResolverInterface
{
    /**
     * Returns if the collection item is visible or not.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @return mixed
     */
    public function isVisible(Item $item);
}
