<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetType;

final class PathInfoPrefix extends PathInfo
{
    public static function getType(): string
    {
        return 'path_info_prefix';
    }
}
