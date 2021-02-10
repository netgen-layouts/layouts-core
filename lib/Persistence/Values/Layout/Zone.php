<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Persistence\Values\Value;
use Netgen\Layouts\Utils\HydratorTrait;

final class Zone extends Value
{
    use HydratorTrait;

    /**
     * Zone identifier.
     */
    public string $identifier;

    /**
     * Layout ID to which this zone belongs.
     */
    public int $layoutId;

    /**
     * Layout UUID to which this zone belongs.
     */
    public string $layoutUuid;

    /**
     * ID of the root block related to the zone.
     */
    public int $rootBlockId;

    /**
     * UUID of layout this zone is linked to or null if no zone is linked.
     */
    public ?string $linkedLayoutUuid;

    /**
     * Zone identifier this zone is linked to or null if no zone is linked.
     */
    public ?string $linkedZoneIdentifier;
}
