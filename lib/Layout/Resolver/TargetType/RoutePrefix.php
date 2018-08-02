<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

final class RoutePrefix extends Route
{
    public static function getType(): string
    {
        return 'route_prefix';
    }
}
