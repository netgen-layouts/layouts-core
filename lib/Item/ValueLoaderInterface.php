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
     * Loads the value from provided ID or null if value cannot be loaded.
     *
     * @param int|string $id
     *
     * @return object|null
     */
    public function load($id);

    /**
     * Loads the value from provided remote ID or null if value cannot be loaded.
     *
     * @param int|string $remoteId
     *
     * @return object|null
     */
    public function loadByRemoteId($remoteId);
}
