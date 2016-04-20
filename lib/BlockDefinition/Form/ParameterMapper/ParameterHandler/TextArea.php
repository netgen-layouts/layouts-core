<?php

namespace Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler;

use Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandlerInterface;
use Netgen\BlockManager\BlockDefinition\Parameter;

class TextArea implements ParameterHandlerInterface
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return 'textarea';
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\BlockDefinition\Parameter $parameter
     *
     * @return array
     */
    public function convertOptions(Parameter $parameter)
    {
        return array();
    }
}
