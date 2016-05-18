<?php

namespace Netgen\BlockManager\Layout\Resolver\Target;

use Netgen\BlockManager\Layout\Resolver\Target;

class PathInfo extends Target
{
    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'path_info';
    }
}
