<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface LayoutViewInterface extends ViewInterface
{
    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout();
}
