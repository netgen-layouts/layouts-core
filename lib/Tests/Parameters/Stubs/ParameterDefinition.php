<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\ParameterDefinition as BaseParameterDefinition;

class ParameterDefinition extends BaseParameterDefinition
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
