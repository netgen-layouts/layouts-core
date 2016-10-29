<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

interface ParameterHandlerInterface
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType();

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     *
     * @return array
     */
    public function convertOptions(ParameterDefinitionInterface $parameterDefinition);

    /**
     * Returns default parameter options for Symfony form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(ParameterDefinitionInterface $parameterDefinition, $parameterName, array $options);

    /**
     * Allows the handler to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function handleForm(ParameterDefinitionInterface $parameterDefinition, FormBuilderInterface $form);
}
