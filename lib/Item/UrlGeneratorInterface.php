<?php

namespace Netgen\BlockManager\Item;

/**
 * Serves as a central point for generating paths to items.
 */
interface UrlGeneratorInterface
{
    /**
     * Returns the item path.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     *
     * @throws \Netgen\BlockManager\Exception\Item\ValueException if value URL generator does not exist
     *
     * @return string
     */
    public function generate(ItemInterface $item);
}
