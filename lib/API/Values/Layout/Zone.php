<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Values\Layout;

use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\API\Values\ValueStatusTrait;
use Netgen\Layouts\Utils\HydratorTrait;
use Ramsey\Uuid\UuidInterface;

final class Zone
{
    use HydratorTrait;
    use ValueStatusTrait;

    public private(set) Status $status;

    /**
     * Returns the zone identifier.
     */
    public private(set) string $identifier;

    /**
     * Returns the UUID of the layout to which this zone belongs.
     */
    public private(set) UuidInterface $layoutId;

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
