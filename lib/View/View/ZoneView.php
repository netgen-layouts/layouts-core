<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Block\BlockList;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\View\View;
use Netgen\Layouts\View\View\ZoneView\ZoneReference;

final class ZoneView extends View implements ZoneViewInterface
{
    public function __construct(ZoneReference $zoneReference, BlockList $blocks)
    {
        $this->parameters['layout'] = $zoneReference->getLayout();
        $this->parameters['zone'] = $zoneReference->getZone();
        $this->parameters['blocks'] = $blocks;
    }

    public function getLayout(): Layout
    {
        return $this->parameters['layout'];
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
