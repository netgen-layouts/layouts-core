<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class Number extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return NumberType::class;
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     *
     * @return array
     */
    public function convertOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        $options = $parameterDefinition->getOptions();

        return array(
            'scale' => $options['scale'],
        );
    }
}
