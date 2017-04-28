<?php

namespace Netgen\BlockManager\Item;

interface ItemLoaderInterface
{
    /**
     * Loads the item from provided value ID and value type.
     *
     * @param int|string $valueId
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If item could not be loaded
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function load($valueId, $valueType);
}
