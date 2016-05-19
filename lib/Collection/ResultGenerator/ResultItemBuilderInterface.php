<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

use Netgen\BlockManager\API\Values\Collection\Item;

interface ResultItemBuilderInterface
{
    /**
     * Builds the result item from provided object.
     *
     * @param mixed $object
     * @param int $position
     *
     * @return \Netgen\BlockManager\Collection\ResultItem
     */
    public function build($object, $position);

    /**
     * Builds the result item from provided collection item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     * @param int $position
     *
     * @return \Netgen\BlockManager\Collection\ResultItem
     */
    public function buildFromItem(Item $item, $position);
}
