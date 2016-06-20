<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class Number extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
    {
        return NumberType::class;
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

        $attributes = array();
        if ($parameterOptions['min'] !== null || $parameterOptions['max'] !== null) {
            if ($parameterOptions['min'] !== null) {
                $attributes['min'] = $parameterOptions['min'];
            }

            if ($parameterOptions['max'] !== null) {
                $attributes['max'] = $parameterOptions['max'];
            }
        }

        return array(
            'attr' => $attributes,
        );
    }
}
