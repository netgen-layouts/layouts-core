<?php

namespace Netgen\BlockManager\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter;

class Text extends Parameter
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
