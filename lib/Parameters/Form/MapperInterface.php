<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\ParameterDefinition;
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
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     *
     * @return array
     */
    public function mapOptions(ParameterDefinition $parameterDefinition);

    /**
     * Allows the mapper to do any kind of processing to created form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     * @param \Netgen\BlockManager\Parameters\ParameterDefinition $parameterDefinition
     */
    public function handleForm(FormBuilderInterface $form, ParameterDefinition $parameterDefinition);
}
