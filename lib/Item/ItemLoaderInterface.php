<?php

namespace Netgen\BlockManager\Item;

/**
 * Item loader is a central point for loading value objects from CMS.
 */
interface ItemLoaderInterface
{
    /**
     * Loads the item from provided value and value type.
     *
     * @param int|string $value
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If item could not be loaded
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function load($value, $valueType);

    /**
     * Loads the item from provided value remote ID and value type.
     *
     * @param int|string $remoteId
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If item could not be loaded
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function loadByRemoteId($remoteId, $valueType);
}
