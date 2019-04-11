<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\View;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\View\ViewInterface;

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
