<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\View\ItemView;

class ItemViewProvider implements ViewProviderInterface
{
    public function provideView($valueObject, array $parameters = array())
    {
        if (!isset($parameters['view_type'])) {
            throw ViewProviderException::noParameter('item', 'view_type');
        }

        if (!is_string($parameters['view_type'])) {
            throw ViewProviderException::invalidParameter('item', 'view_type', 'string');
        }

        return new ItemView(
            array(
                'item' => $valueObject,
                'view_type' => $parameters['view_type'],
            )
        );
    }

    public function supports($valueObject)
    {
        return $valueObject instanceof ItemInterface;
    }
}
