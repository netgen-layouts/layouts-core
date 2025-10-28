<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Utils\HydratorTrait;

final class ZoneUpdateStruct
{
    use HydratorTrait;

    /**
     * New linked zone.
     *
     * Set to "false" to remove the link.
     */
    public Zone|false|null $linkedZone;
}
