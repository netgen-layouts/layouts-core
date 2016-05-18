<?php

namespace Netgen\BlockManager\View;

interface LayoutViewInterface extends ViewInterface
{
    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout();
}
