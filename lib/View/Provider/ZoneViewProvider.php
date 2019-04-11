<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Provider;

use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\View\View\ZoneView;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;
use Netgen\Layouts\View\ViewInterface;

final class ZoneViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        if (!isset($parameters['blocks'])) {
            throw ViewProviderException::noParameter('zone', 'blocks');
        }

        if (!$parameters['blocks'] instanceof BlockList) {
            throw ViewProviderException::invalidParameter('zone', 'blocks', BlockList::class);
        }

        return new ZoneView($value, $parameters['blocks']);
    }

    public function supports($value): bool
    {
        return $value instanceof ZoneReference;
    }
}
