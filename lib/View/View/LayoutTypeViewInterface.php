<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\ViewInterface;

interface LayoutTypeViewInterface extends ViewInterface
{
    /**
     * Returns the layout type.
     *
     * @return \Netgen\BlockManager\Layout\Type\LayoutTypeInterface
     */
    public function getLayoutType();
}
