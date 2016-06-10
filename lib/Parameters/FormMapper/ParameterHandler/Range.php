<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

class Range extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
    {
        return RangeType::class;
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
            'attr' => array(
                'min' => $parameterOptions['min'],
                'max' => $parameterOptions['max'],
            ),
        );
    }
}
