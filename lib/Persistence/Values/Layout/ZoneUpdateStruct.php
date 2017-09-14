<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\ValueObject;

class ZoneUpdateStruct extends ValueObject
{
    /**
     * New linked zone.
     *
     * @var \Netgen\BlockManager\Persistence\Values\Layout\Zone
     */
    public $linkedZone;
}
