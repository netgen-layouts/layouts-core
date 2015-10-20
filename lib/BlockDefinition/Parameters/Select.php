<?php

namespace Netgen\BlockManager\BlockDefinition\Parameters;

use Netgen\BlockManager\BlockDefinition\Parameter;

class Select extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'select';
    }
}
