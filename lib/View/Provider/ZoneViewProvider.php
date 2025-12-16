<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\View\View\ZoneView;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;

use function array_key_exists;

/**
 * @implements \Netgen\Layouts\View\Provider\ViewProviderInterface<\Netgen\Layouts\View\View\ZoneView\ZoneReference>
 */
final class ZoneViewProvider implements ViewProviderInterface
{
    public function provideView(object $value, array $parameters = []): ZoneView
    {
        if (!array_key_exists('blocks', $parameters)) {
            throw ViewProviderException::noParameter('zone', 'blocks');
        }

        if (!$parameters['blocks'] instanceof BlockList) {
            throw ViewProviderException::invalidParameter('zone', 'blocks', BlockList::class);
        }

        return new ZoneView($value, $parameters['blocks']);
    }

    public function supports(object $value): bool
    {
        return $value instanceof ZoneReference;
    }
}
