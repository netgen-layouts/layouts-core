<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\BlockList;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\Exception\View\ViewProviderException;
use Netgen\BlockManager\View\View\ZoneView;
use Netgen\BlockManager\View\ViewInterface;

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
        return $value instanceof Zone;
    }
}
