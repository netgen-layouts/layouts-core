<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetType;

final class RoutePrefix extends Route
{
    public static function getType(): string
    {
        return 'route_prefix';
    }
}
