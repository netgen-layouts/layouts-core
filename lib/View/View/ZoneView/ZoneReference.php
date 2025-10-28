<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View\ZoneView;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;

final class ZoneReference
{
    public function __construct(
        private Layout $layout,
        private string $zoneIdentifier,
    ) {}

    public function getLayout(): Layout
    {
        return $this->layout;
    }

    public function getZone(): Zone
    {
        return $this->layout->getZone($this->zoneIdentifier);
    }
}
