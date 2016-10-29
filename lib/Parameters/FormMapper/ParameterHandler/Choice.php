<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class Choice extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return ChoiceType::class;
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
            'multiple' => $options['multiple'],
            'choices' => is_callable($options['options']) ?
                $options['options']() :
                $options['options'],
            'choices_as_values' => true,
        );
    }
}
