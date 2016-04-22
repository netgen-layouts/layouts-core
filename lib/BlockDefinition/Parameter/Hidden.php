<?php

namespace Netgen\BlockManager\BlockDefinition\Parameter;

class Hidden extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'hidden';
    }
}
