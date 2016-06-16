<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Choice extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
    {
        return ChoiceType::class;
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    protected function convertOptions(ParameterInterface $parameter)
    {
        $parameterOptions = $parameter->getOptions();

        return array(
            'multiple' => $parameterOptions['multiple'],
            'choices' => is_callable($parameterOptions['options']) ?
                $parameterOptions['options']() :
                $parameterOptions['options'],
            'choices_as_values' => true,
        );
    }
}
