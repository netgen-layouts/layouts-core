<?php

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\View\View;
use Netgen\BlockManager\API\Values\Page\LayoutInfo;

class LayoutInfoView extends View implements LayoutInfoViewInterface
{
    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Values\Page\LayoutInfo $layout
     */
    public function __construct(LayoutInfo $layout)
    {
        $this->valueObject = $layout;
        $this->internalParameters['layout'] = $layout;
    }

    /**
     * Returns the layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\LayoutInfo
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
        return 'layout_info_view';
    }
}
