<?php

namespace Netgen\BlockManager\BlockDefinition\Tests\Stubs;

use Netgen\BlockManager\BlockDefinition\Parameter as BaseParameter;

class Parameter extends BaseParameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'stub';
    }
}
