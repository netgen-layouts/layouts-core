<?php

namespace Netgen\BlockManager\Collection\Result;

use Netgen\BlockManager\Item\ItemInterface;

interface CollectionItemBased extends ItemInterface
{
    /**
     * Returns the collection item.
     *
     * @return \Netgen\BlockManager\API\Values\Collection\Item
     */
    public function getCollectionItem();
}
