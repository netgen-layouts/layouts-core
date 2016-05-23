<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;

class LayoutViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($value)
    {
        /** @var \Netgen\BlockManager\API\Values\Page\Layout $value */
        $layoutView = new LayoutView($value);

        return $layoutView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function supports($value)
    {
        return $value instanceof Layout;
    }
}
