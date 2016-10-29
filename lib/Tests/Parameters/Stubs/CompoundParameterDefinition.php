<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\CompoundParameterDefinition as BaseCompoundParameterDefinition;

class CompoundParameterDefinition extends BaseCompoundParameterDefinition
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'compound';
    }
}
