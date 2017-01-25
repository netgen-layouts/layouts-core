<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Exception\RuntimeException;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\View\ItemView;

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
        if (!isset($parameters['view_type'])) {
            throw new RuntimeException('To build the item view, you need to provide the "view_type" parameter.');
        }

        if (!is_string($parameters['view_type'])) {
            throw new RuntimeException('To build the item view, "view_type" parameter needs to be a string.');
        }

        return new ItemView(
            array(
                'item' => $valueObject,
                'view_type' => $parameters['view_type'],
            )
        );
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
        return $valueObject instanceof ItemInterface;
    }
}
