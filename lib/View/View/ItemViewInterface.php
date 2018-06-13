<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface ItemViewInterface extends ViewInterface
{
    /**
     * Returns the item.
     *
     * @return \Netgen\BlockManager\Item\ItemInterface
     */
    public function getItem();

    /**
     * Returns the view type.
     *
     * @return string
     */
    public function getViewType();
}
