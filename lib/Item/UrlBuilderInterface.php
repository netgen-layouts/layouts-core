<?php

namespace Netgen\BlockManager\Item;

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
