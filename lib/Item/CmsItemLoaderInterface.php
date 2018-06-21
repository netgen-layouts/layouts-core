<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

/**
 * CMS item loader is a central point for loading items representing values from CMS.
 */
interface CmsItemLoaderInterface
{
    /**
     * Loads the CMS item from provided value and value type.
     *
     * @param int|string $value
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If item could not be loaded
     *
     * @return \Netgen\BlockManager\Item\CmsItemInterface
     */
    public function load($value, string $valueType): CmsItemInterface;

    /**
     * Loads the CMS item from provided value remote ID and value type.
     *
     * @param int|string $remoteId
     * @param string $valueType
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If item could not be loaded
     *
     * @return \Netgen\BlockManager\Item\CmsItemInterface
     */
    public function loadByRemoteId($remoteId, string $valueType): CmsItemInterface;
}
