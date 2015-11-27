<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Stubs;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;

class TargetBuilderReturnsFalse implements TargetBuilderInterface
{
    /**
     * Builds the target object that will be used to search for resolver rules.
     *
     * @return \Netgen\BlockManager\LayoutResolver\Target|null
     */
    public function buildTarget()
    {
        return false;
    }
}
