<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\ViewInterface;

interface ItemViewInterface extends ViewInterface
{
    /**
     * Returns the item.
     */
    public function getItem(): ItemInterface;

    /**
     * Returns the view type.
     */
    public function getViewType(): string;
}
