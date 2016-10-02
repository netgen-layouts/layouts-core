<?php

namespace Netgen\BlockManager\Item;

interface ItemBuilderInterface
{
    /**
     * Builds the item from provided object.
     *
     * @param mixed $object
     *
     * @throws \Netgen\BlockManager\Exception\RuntimeException If value cannot be built
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function build($object);
}
