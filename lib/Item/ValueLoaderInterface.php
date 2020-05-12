<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

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
     * Any error situations (like non existing values or values which cannot be used
     * due to various conditions) need to be handled by the implementation (e.g. logging)
     * and just return null.
     *
     * @param int|string $id
     */
    public function load($id): ?object;

    /**
     * Loads the value from provided remote ID or null if value cannot be loaded.
     *
     * Any error situations (like non existing values or values which cannot be used
     * due to various conditions) need to be handled by the implementation (e.g. logging)
     * and just return null.
     *
     * @param int|string $remoteId
     */
    public function loadByRemoteId($remoteId): ?object;
}
