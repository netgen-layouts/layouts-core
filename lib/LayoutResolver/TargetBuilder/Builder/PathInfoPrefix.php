<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder;

class PathInfoPrefix extends PathInfo
{
    /**
     * Returns the unique identifier of the target this builder builds.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'path_info_prefix';
    }
}
