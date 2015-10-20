<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Page\Layout;

interface LayoutViewInterface extends ViewInterface
{
    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout();

    /**
     * Sets the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function setLayout(Layout $layout);
}
