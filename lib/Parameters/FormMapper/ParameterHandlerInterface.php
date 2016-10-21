<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
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
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function convertOptions(ParameterInterface $parameter);

    /**
     * Returns default parameter options for Symfony form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(ParameterInterface $parameter, $parameterName, array $options);

    /**
     * Allows the handler to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function processForm(ParameterInterface $parameter, FormBuilderInterface $form);
}
