<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
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
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     *
     * @return array
     */
    public function convertOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        $options = $parameterDefinition->getOptions();

        return array(
            'attr' => array(
                'min' => $options['min'],
                'max' => $options['max'],
            ),
        );
    }
}
