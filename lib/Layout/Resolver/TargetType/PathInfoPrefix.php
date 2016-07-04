<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

class PathInfoPrefix extends PathInfo
{
    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType()
    {
        return 'path_info_prefix';
    }
}
