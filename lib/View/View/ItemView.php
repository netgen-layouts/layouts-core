<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\View;

class ItemView extends View implements ItemViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Item\ItemInterface $item
     * @param string $viewType
     */
    public function __construct(ItemInterface $item, $viewType)
    {
        $this->valueObject = $item;
        $this->internalParameters['item'] = $item;
        $this->internalParameters['viewType'] = $viewType;
    }

    /**
     * Returns the item.
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function getItem()
    {
        return $this->valueObject;
    }

    /**
     * Returns the view type.
     *
     * @return string
     */
    public function getViewType()
    {
        return $this->internalParameters['viewType'];
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
