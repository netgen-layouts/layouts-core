<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

final class PathInfoPrefix extends PathInfo
{
    public function getType()
    {
        return 'path_info_prefix';
    }
}
