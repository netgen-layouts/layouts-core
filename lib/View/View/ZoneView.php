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
    public string $identifier {
        get => 'zone';
    }

    public Layout $layout {
        get => $this->getParameter('layout');
    }

    public Zone $zone {
        get => $this->getParameter('zone');
    }

    public function __construct(ZoneReference $zoneReference, BlockList $blocks)
    {
        $this
            ->addInternalParameter('layout', $zoneReference->layout)
            ->addInternalParameter('zone', $zoneReference->zone)
            ->addInternalParameter('blocks', $blocks);
    }
}
