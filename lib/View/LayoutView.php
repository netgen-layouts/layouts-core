<?php

namespace Netgen\BlockManager\View;

use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutView extends View implements LayoutViewInterface
{
    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout()
    {
        return $this->value;
    }

    /**
     * Sets the layout.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Layout $layout
     */
    public function setLayout(Layout $layout)
    {
        $this->value = $layout;
        $this->internalParameters['layout'] = $this->value;
    }

    /**
     * Returns the view alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return 'layout_view';
    }
}
