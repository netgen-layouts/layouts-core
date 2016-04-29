<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandlerInterface;
use Netgen\BlockManager\Parameters\Parameter;

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
     * @param \Netgen\BlockManager\Parameters\Parameter $parameter
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
