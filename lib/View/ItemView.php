<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\API\Values\Page\Block;

class ItemView extends View implements ItemViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\Item $item
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     */
    public function __construct(Item $item, Block $block)
    {
        $this->valueObject = $item;
        $this->internalParameters['item'] = $item;
        $this->internalParameters['block'] = $block;
    }

    /**
     * Returns the item.
     *
     * @return \Netgen\BlockManager\Item\Item
     */
    public function getItem()
    {
        return $this->valueObject;
    }

    /**
     * Returns the block.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Block
     */
    public function getBlock()
    {
        return $this->internalParameters['block'];
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'item_view';
    }
}
