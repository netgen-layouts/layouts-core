<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\View\ViewInterface;

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
