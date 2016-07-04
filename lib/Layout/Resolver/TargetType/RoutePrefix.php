<?php

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

class RoutePrefix extends Route
{
    /**
     * Returns the target type.
     *
     * @return string
     */
    public function getType()
    {
        return 'route_prefix';
    }
}
