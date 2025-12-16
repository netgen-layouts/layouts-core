<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Item\CmsItemInterface;
use Netgen\Layouts\View\View\ItemView;

use function array_key_exists;
use function is_string;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\Item\CmsItemInterface>
 */
final class ItemViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): ItemView
    {
        if (!array_key_exists('view_type', $parameters)) {
            throw ViewProviderException::noParameter('item', 'view_type');
        }

        if (!is_string($parameters['view_type'])) {
            throw ViewProviderException::invalidParameter('item', 'view_type', 'string');
        }

        return new ItemView($value, $parameters['view_type']);
    }

    public function supports(object $value): bool
    {
        return $value instanceof CmsItemInterface;
    }
}
