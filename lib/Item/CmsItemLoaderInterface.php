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
     */
    public function load(int|string $id, string $valueType): CmsItemInterface;

    /**
     * Loads the CMS item from provided value remote ID and value type.
     */
    public function loadByRemoteId(int|string $remoteId, string $valueType): CmsItemInterface;
}
