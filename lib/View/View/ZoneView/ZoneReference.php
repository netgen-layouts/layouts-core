<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View\ZoneView;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;

final class ZoneReference
{
    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    private $layout;

    /**
     * @var string
     */
    private $zoneIdentifier;

    public function __construct(Layout $layout, string $zoneIdentifier)
    {
        $this->layout = $layout;
        $this->zoneIdentifier = $zoneIdentifier;
    }

    public function getLayout(): Layout
    {
        return $this->layout;
    }

    public function getZone(): Zone
    {
        return $this->layout->getZone($this->zoneIdentifier);
    }
}
