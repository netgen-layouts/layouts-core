<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Mapper used to create a Symfony form for editing a parameter type.
 */
interface MapperInterface
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType();

    /**
     * Maps parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     *
     * @return array
     */
    public function mapOptions(ParameterDefinitionInterface $parameterDefinition);

    /**
     * Allows the mapper to do any kind of processing to created form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     */
    public function handleForm(FormBuilderInterface $form, ParameterDefinitionInterface $parameterDefinition);
}
