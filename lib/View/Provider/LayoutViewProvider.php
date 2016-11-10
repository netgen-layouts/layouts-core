<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\View\View\LayoutView;

class LayoutViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array())
    {
        return new LayoutView($valueObject);
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof Layout;
    }
}
