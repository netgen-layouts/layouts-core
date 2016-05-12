<?php

namespace Netgen\BlockManager\Collection\ResultGenerator;

use Netgen\BlockManager\API\Values\Collection\Item;

interface ResultValueBuilderInterface
{
    /**
     * Builds the result value from provided object.
     *
     * @param mixed $object
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Collection\ResultValue
     */
    public function build($object);

    /**
     * Builds the result value from provided item.
     *
     * @param \Netgen\BlockManager\API\Values\Collection\Item $item
     *
     * @throws \RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Collection\ResultValue
     */
    public function buildFromItem(Item $item);
}
