<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Value;

final class ZoneUpdateStruct extends Value
{
    /**
     * New linked zone.
     *
     * @var \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public $linkedZone;
}
