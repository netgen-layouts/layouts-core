<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\View\View\ItemView;
use Netgen\BlockManager\Exception\RuntimeException;

class ItemViewProvider implements ViewProviderInterface
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
        if (!isset($parameters['viewType'])) {
            throw new RuntimeException('To build the item view, you need to provide the "viewType" parameter.');
        }

        if (!is_string($parameters['viewType'])) {
            throw new RuntimeException('To build the item view, "viewType" parameter needs to be a string.');
        }

        /** @var \Netgen\BlockManager\Item\Item $valueObject */
        $itemView = new ItemView($valueObject, $parameters['viewType']);

        return $itemView;
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
        return $valueObject instanceof Item;
    }
}
