<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface;
use Netgen\BlockManager\Parameters\Parameter;

class Text implements ParameterHandlerInterface
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return 'text';
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\Parameter $parameter
     *
     * @return array
     */
    public function convertOptions(Parameter $parameter)
    {
        return array();
    }
}
