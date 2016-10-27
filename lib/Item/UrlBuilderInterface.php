<?php

namespace Netgen\BlockManager\Item;

interface UrlBuilderInterface
{
    /**
     * Returns the item URL.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     *
     * @return string
     */
    public function getUrl(ItemInterface $item);
}
