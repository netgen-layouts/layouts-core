<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Layout;

use Netgen\Layouts\Utils\HydratorTrait;

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
     * Zone that will be linked to the new zone.
     *
     * @var \Netgen\Layouts\Persistence\Values\Layout\Zone|null
     */
    public $linkedZone;
}
