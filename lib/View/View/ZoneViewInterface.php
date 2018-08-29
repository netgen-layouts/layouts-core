<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\View;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\Layout\Zone;
use Netgen\BlockManager\View\ViewInterface;

interface ZoneViewInterface extends ViewInterface
{
    /**
     * Returns the layout that the zone belongs to.
     */
    public function getLayout(): Layout;

    /**
     * Returns the zone.
     */
    public function getZone(): Zone;
}
