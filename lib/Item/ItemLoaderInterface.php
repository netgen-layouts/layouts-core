<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

/**
 * Item loader is a central point for loading values from CMS.
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
    public function load($value, string $valueType): ItemInterface;

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
    public function loadByRemoteId($remoteId, string $valueType): ItemInterface;
}
