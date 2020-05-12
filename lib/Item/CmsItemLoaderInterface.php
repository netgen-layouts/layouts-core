<?php

declare(strict_types=1);

namespace Netgen\Layouts\Item;

/**
 * CMS item loader is a central point for loading items representing values from CMS.
 */
interface CmsItemLoaderInterface
{
    /**
     * Loads the CMS item from provided ID and value type.
     *
     * @param int|string $id
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If item could not be loaded
     */
    public function load($id, string $valueType): CmsItemInterface;

    /**
     * Loads the CMS item from provided value remote ID and value type.
     *
     * @param int|string $remoteId
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If item could not be loaded
     */
    public function loadByRemoteId($remoteId, string $valueType): CmsItemInterface;
}
