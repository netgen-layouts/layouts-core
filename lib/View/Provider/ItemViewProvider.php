<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\View\View\ItemView;
use Netgen\Layouts\View\ViewInterface;

use function is_string;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\Item\CmsItemInterface>
 */
final class ItemViewProvider implements ViewProviderInterface
{
    public function provideView(mixed $value, array $parameters = []): ViewInterface
    {
        if (!isset($parameters['view_type'])) {
            throw ViewProviderException::noParameter('item', 'view_type');
        }

        if (!is_string($parameters['view_type'])) {
            throw ViewProviderException::invalidParameter('item', 'view_type', 'string');
        }

        return new ItemView($value, $parameters['view_type']);
    }

    public function supports(mixed $value): bool
    {
        return $value instanceof CmsItemInterface;
    }
}
