<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\Item\CmsItemInterface;
use Netgen\BlockManager\View\ViewInterface;

interface ItemViewInterface extends ViewInterface
{
    /**
     * Returns the CMS item.
     */
    public function getItem(): CmsItemInterface;

    /**
     * Returns the view type with which the CMS item will be rendered.
     */
    public function getViewType(): string;
}
