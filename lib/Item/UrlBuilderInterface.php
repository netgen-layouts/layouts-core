<?php

namespace Netgen\BlockManager\Item;

/**
 * Serves as a central point for generating URLs/paths to items.
 */
interface UrlBuilderInterface
{
    /**
     * Returns the item URL.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     *
     * @throws \Netgen\BlockManager\Exception\Item\ValueException if value URL builder does not exist
     *
     * @return string
     */
    public function getUrl(ItemInterface $item);
}
