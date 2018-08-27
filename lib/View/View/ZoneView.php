<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\Block\BlockList;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\View\View;

final class ZoneView extends View implements ZoneViewInterface
{
    public function __construct(Zone $zone, BlockList $blocks)
    {
        $this->parameters['zone'] = $zone;
        $this->parameters['blocks'] = $blocks;
    }

    public function getZone(): Zone
    {
        return $this->parameters['zone'];
    }

    public static function getIdentifier(): string
    {
        return 'zone';
    }
}
