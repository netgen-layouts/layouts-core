<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver\Stubs;

use Netgen\BlockManager\Layout\Resolver\TargetBuilder\TargetBuilderInterface;

class TargetBuilderReturnsFalse implements TargetBuilderInterface
{
    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\Layout\Resolver\Target|null
     */
    public function buildTarget()
    {
        return false;
    }
}
