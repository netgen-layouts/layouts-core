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
        get => $this->zoneReference->layout;
    }

    public Zone $zone {
        get => $this->zoneReference->zone;
    }

    public function __construct(
        private ZoneReference $zoneReference,
        public private(set) BlockList $blocks,
    ) {
        $this
            ->addInternalParameter('layout', $this->zoneReference->layout)
            ->addInternalParameter('zone', $this->zoneReference->zone)
            ->addInternalParameter('blocks', $this->blocks);
    }
}
