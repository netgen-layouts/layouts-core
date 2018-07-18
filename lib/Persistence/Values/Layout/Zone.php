<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Utils\HydratorTrait;

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
     * @var int|string
     */
    public $layoutId;

    /**
     * ID of the root block related to the zone.
     *
     * @var int
     */
    public $rootBlockId;

    /**
     * ID of layout this zone is linked to or null if no zone is linked.
     *
     * @var int|string|null
     */
    public $linkedLayoutId;

    /**
     * Zone identifier this zone is linked to or null if no zone is linked.
     *
     * @var string|null
     */
    public $linkedZoneIdentifier;
}
