<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface LayoutInfoViewInterface extends ViewInterface
{
    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutInfo
     */
    public function getLayout();
}
