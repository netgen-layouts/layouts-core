<?php

namespace Netgen\BlockManager\Persistence\Values;

use Netgen\BlockManager\ValueObject;

class ZoneUpdateStruct extends ValueObject
{
    /**
     * @var \Netgen\BlockManager\Persistence\Values\Page\Zone
     */
    public $linkedZone;
}
