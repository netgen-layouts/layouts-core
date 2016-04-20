<?php

namespace Netgen\BlockManager\BlockDefinition\Parameter;

use Netgen\BlockManager\BlockDefinition\Parameter;

class TextArea extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'textarea';
    }
}
