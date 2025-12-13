<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Symfony\Component\Uid\Uuid;

final class Zone
{
    use HydratorTrait;
    use ValueStatusTrait;

    /**
     * Returns the zone identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns the UUID of the layout to which this zone belongs.
     */
    public private(set) Uuid $layoutId;

    /**
     * Returns the linked zone or null if no linked zone exists.
     */
    public private(set) ?Zone $linkedZone;

    /**
     * Returns if the zone has a linked zone.
     */
    public bool $hasLinkedZone {
        get => $this->linkedZone instanceof self;
    }
}
