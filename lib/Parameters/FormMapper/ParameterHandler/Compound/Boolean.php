<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler\Compound;

use Netgen\BlockManager\Parameters\FormMapper\CompoundParameterHandler;
use Netgen\BlockManager\Parameters\Form\CompoundBooleanType;
use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;

class Boolean extends CompoundParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return CompoundBooleanType::class;
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
        return array(
            'reverse' => $parameterDefinition->getOptions()['reverse'],
        ) + parent::convertOptions($parameterDefinition);
    }

    /**
     * Returns default parameter options for Symfony form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(ParameterDefinitionInterface $parameterDefinition, $parameterName, array $options)
    {
        return array(
            'checkbox_required' => $parameterDefinition->isRequired(),
            'checkbox_label' => $options['label_prefix'] . '.' . $parameterName,
            'checkbox_property_path' => $options['property_path_prefix'] . '[' . $parameterName . ']',
        ) + parent::getDefaultOptions($parameterDefinition, $parameterName, $options);
    }
}
