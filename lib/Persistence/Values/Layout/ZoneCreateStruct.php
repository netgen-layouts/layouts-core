<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Utils\HydratorTrait;

final class ZoneCreateStruct
{
    use HydratorTrait;

    /**
     * Identifier for the new zone.
     *
     * @var string
     */
    public $identifier;

    /**
     * Identifier of the zone that will be linked to the new zone.
     *
     * @var string|null
     */
    public $linkedZoneIdentifier;

    /**
     * ID of the layout where the linked zone is located.
     *
     * @var int|string|null
     */
    public $linkedLayoutId;
}
