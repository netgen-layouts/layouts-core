<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Value;

final class ZoneCreateStruct extends Value
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
