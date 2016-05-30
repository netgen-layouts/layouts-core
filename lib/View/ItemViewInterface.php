<?php

namespace Netgen\BlockManager\View;

interface ItemViewInterface extends ViewInterface
{
    /**
     * Returns the item.
     *
     * @return \Netgen\BlockManager\Item\Item
     */
    public function getItem();

    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock();
}
