<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

class PathInfoPrefix extends PathInfo
{
    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'path_info_prefix';
    }
}
