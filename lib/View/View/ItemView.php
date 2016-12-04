<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;

class ItemView extends View implements ItemViewInterface
{
    /**
     * Returns the item.
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function getItem()
    {
        return $this->parameters['item'];
    }

    /**
     * Returns the view type.
     *
     * @return string
     */
    public function getViewType()
    {
        return $this->parameters['viewType'];
    }

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'item_view';
    }
}
