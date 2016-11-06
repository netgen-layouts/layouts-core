<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function mapOptions(ParameterInterface $parameter);

    /**
     * Allows the mapper to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function handleForm(ParameterInterface $parameter, FormBuilderInterface $form);
}
