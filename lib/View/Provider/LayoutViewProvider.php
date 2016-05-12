<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Core\Values\Value;
use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;

class LayoutViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param \Netgen\BlockManager\Core\Values\Value $value
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView(Value $value)
    {
        /** @var \Netgen\BlockManager\API\Values\Page\Layout $value */
        $layoutView = new LayoutView();

        $layoutView->setLayout($value);

        return $layoutView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param \Netgen\BlockManager\Core\Values\Value $value
     *
     * @return bool
     */
    public function supports(Value $value)
    {
        return $value instanceof Layout;
    }
}
