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
     * If value URL generator for a specific value type does not exist, this simply returns null.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     *
     * @return string|null
     */
    public function generate(ItemInterface $item);
}
