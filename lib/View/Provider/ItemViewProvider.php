<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\Item\ItemInterface;
use Netgen\BlockManager\View\View\ItemView;
use Netgen\BlockManager\View\ViewInterface;

final class ItemViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        if (!isset($parameters['view_type'])) {
            throw ViewProviderException::noParameter('item', 'view_type');
        }

        if (!is_string($parameters['view_type'])) {
            throw ViewProviderException::invalidParameter('item', 'view_type', 'string');
        }

        return new ItemView($value, $parameters['view_type']);
    }

    public function supports($value): bool
    {
        return $value instanceof ItemInterface;
    }
}
