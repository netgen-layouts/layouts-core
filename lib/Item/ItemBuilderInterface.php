<?php

namespace Netgen\BlockManager\Item;

interface ItemBuilderInterface
{
    /**
     * Builds the item from provided object.
     *
     * @param mixed $object
     *
     * @throws \Netgen\BlockManager\Exception\Item\ValueException if value converter does not exist
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function build($object);
}
