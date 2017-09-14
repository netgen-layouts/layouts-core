<?php

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\ValueObject;

class ZoneCreateStruct extends ValueObject
{
    /**
     * Identifier for the new zone.
     *
     * @var string
     */
    public $identifier;

    /**
     * Identifier of the zone that will be linked to the new zone.
     *
     * @var string
     */
    public $linkedZoneIdentifier;

    /**
     * ID of the layout where the linked zone is located.
     *
     * @var int|string
     */
    public $linkedLayoutId;
}
