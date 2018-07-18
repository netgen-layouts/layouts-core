<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Values\Layout;

use Netgen\BlockManager\Utils\HydratorTrait;

final class ZoneUpdateStruct
{
    use HydratorTrait;

    /**
     * New linked zone.
     *
     * @var \Netgen\BlockManager\Persistence\Values\Layout\Zone|null
     */
    public $linkedZone;
}
