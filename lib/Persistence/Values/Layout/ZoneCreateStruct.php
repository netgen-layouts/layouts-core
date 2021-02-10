<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Utils\HydratorTrait;

final class ZoneCreateStruct
{
    use HydratorTrait;

    /**
     * Identifier for the new zone.
     */
    public string $identifier;

    /**
     * Zone that will be linked to the new zone.
     */
    public ?Zone $linkedZone;
}
