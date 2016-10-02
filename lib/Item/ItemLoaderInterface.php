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
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function load($valueId, $valueType);
}
