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
    public function getFormType()
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
    public function convertOptions(ParameterInterface $parameter)
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
