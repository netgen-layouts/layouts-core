<?php

namespace Netgen\BlockManager\BlockDefinition\Form\ParameterMapper;

use Netgen\BlockManager\BlockDefinition\Parameter\Parameter;

interface ParameterHandlerInterface
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType();

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\BlockDefinition\Parameter\Parameter $parameter
     *
     * @return array
     */
    public function convertOptions(Parameter $parameter);
}
