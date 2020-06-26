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
     *
     * @var string
     */
    public $identifier;

    /**
     * Layout ID to which this zone belongs.
     *
     * @var int
     */
    public $layoutId;

    /**
     * Layout UUID to which this zone belongs.
     *
     * @var string
     */
    public $layoutUuid;

    /**
     * ID of the root block related to the zone.
     *
     * @var int
     */
    public $rootBlockId;

    /**
     * UUID of layout this zone is linked to or null if no zone is linked.
     *
     * @var string|null
     */
    public $linkedLayoutUuid;

    /**
     * Zone identifier this zone is linked to or null if no zone is linked.
     *
     * @var string|null
     */
    public $linkedZoneIdentifier;
}
