<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutView extends View implements LayoutViewInterface
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Layout
     */
    protected $layout;

    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Sets the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
        $this->parameters['layout'] = $this->layout;
    }
}
