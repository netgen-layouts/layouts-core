<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\CacheableViewTrait;
use Netgen\BlockManager\View\View;

class LayoutView extends View implements LayoutViewInterface
{
    use CacheableViewTrait;

    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function getLayout()
    {
        return $this->parameters['layout'];
    }

    /**
     * Returns the view identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'layout_view';
    }
}
