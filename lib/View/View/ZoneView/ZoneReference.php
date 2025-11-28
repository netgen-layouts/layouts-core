<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View\ZoneView;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;

final class ZoneReference
{
    public Zone $zone {
        get => $this->layout->getZone($this->zoneIdentifier);
    }

    public function __construct(
        public private(set) Layout $layout,
        private string $zoneIdentifier,
    ) {}
}
