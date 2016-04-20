<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Stubs;

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
        return 'text';
    }
}
