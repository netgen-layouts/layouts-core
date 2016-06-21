<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

class RoutePrefix extends Route
{
    /**
     * Returns the target type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'route_prefix';
    }
}
