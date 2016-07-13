<?php

namespace Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout;

interface LayoutInterface
{
    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutInfo
     */
    public function getLayout();
}
