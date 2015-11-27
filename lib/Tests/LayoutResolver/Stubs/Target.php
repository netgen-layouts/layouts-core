<?php

namespace Netgen\BlockManager\Tests\LayoutResolver\Stubs;

use Netgen\BlockManager\LayoutResolver\Target as BaseTarget;

class Target extends BaseTarget
{
    /**
     * Returns the unique identifier of the target
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'target';
    }
}
