<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Item;

/**
 * Value loader is used to load the CMS value by its ID.
 * It is used and injected into CmsItemLoaderInterface which is a central
 * point for loading CMS objects for use by blocks.
 */
interface ValueLoaderInterface
{
    /**
     * Loads the value from provided ID.
     *
     * @param int|string $id
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If value cannot be loaded
     *
     * @return mixed
     */
    public function load($id);

    /**
     * Loads the value from provided remote ID.
     *
     * @param int|string $remoteId
     *
     * @throws \Netgen\BlockManager\Exception\Item\ItemException If value cannot be loaded
     *
     * @return mixed
     */
    public function loadByRemoteId($remoteId);
}
