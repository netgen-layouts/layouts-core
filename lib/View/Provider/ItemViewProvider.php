<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\View\ItemView;
use RuntimeException;

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
        if (!isset($parameters['block'])) {
            throw new RuntimeException('To build the item view, you need to provide the "block" parameter.');
        }

        if (!$parameters['block'] instanceof Block) {
            throw new RuntimeException('To build the item view, "block" parameter needs to be an instance of a Block.');
        }

        /** @var \Netgen\BlockManager\Item\Item $valueObject */
        $itemView = new ItemView($valueObject, $parameters['block']);

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
