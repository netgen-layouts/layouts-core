<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class ParameterHandler implements ParameterHandlerInterface
{
    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     *
     * @return array
     */
    public function convertOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return array();
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
            'required' => $parameterDefinition->isRequired(),
            'label' => $options['label_prefix'] . '.' . $parameterName,
            'property_path' => $options['property_path_prefix'] . '[' . $parameterName . ']',
        );
    }

    /**
     * Allows the handler to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function handleForm(ParameterDefinitionInterface $parameterDefinition, FormBuilderInterface $form)
    {
    }
}
