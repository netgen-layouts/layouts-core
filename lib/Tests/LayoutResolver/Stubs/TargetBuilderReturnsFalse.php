<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Stubs;

use Netgen\BlockManager\LayoutResolver\TargetBuilder\TargetBuilderInterface;

class TargetBuilderReturnsFalse implements TargetBuilderInterface
{
    /**
     * Returns the unique identifier of the target this builder builds.
     *
     * @return string
     */
    public function getTargetIdentifier()
    {
        return 'target';
    }

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
