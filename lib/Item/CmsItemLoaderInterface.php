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
     * @param string $valueType
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If item could not be loaded
     *
     * @return \Netgen\Layouts\Item\CmsItemInterface
     */
    public function load($id, string $valueType): CmsItemInterface;

    /**
     * Loads the CMS item from provided value remote ID and value type.
     *
     * @param int|string $remoteId
     * @param string $valueType
     *
     * @throws \Netgen\Layouts\Exception\Item\ItemException If item could not be loaded
     *
     * @return \Netgen\Layouts\Item\CmsItemInterface
     */
    public function loadByRemoteId($remoteId, string $valueType): CmsItemInterface;
}
