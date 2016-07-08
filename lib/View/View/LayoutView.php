<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;
use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutView extends View implements LayoutViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function __construct(Layout $layout)
    {
        $this->valueObject = $layout;
        $this->internalParameters['layout'] = $layout;
    }

    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout()
    {
        return $this->valueObject;
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
