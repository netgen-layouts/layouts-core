<?php

namespace Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandler;

use Netgen\BlockManager\BlockDefinition\Form\ParameterMapper\ParameterHandlerInterface;
use Netgen\BlockManager\BlockDefinition\Parameter\Parameter;

class Select implements ParameterHandlerInterface
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return 'choice';
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\BlockDefinition\Parameter\Parameter $parameter
     *
     * @return array
     */
    public function convertOptions(Parameter $parameter)
    {
        $parameterOptions = $parameter->getOptions();

        return array(
            'multiple' => $parameterOptions['multiple'],
            'choices' => $parameterOptions['options'],
            'choices_as_values' => true,
        );
    }
}
