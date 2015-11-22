<?php

namespace Netgen\BlockManager\LayoutResolver\TargetBuilder\Builder;

class RoutePrefix extends Route
{
    /**
     * Returns the unique identifier of the target this builder builds.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'route_prefix';
    }
}
