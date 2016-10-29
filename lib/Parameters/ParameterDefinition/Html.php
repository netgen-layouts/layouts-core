<?php

namespace Netgen\BlockManager\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition;

class Html extends ParameterDefinition
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'html';
    }
}
