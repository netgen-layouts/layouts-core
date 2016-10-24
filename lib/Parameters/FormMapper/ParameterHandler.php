<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class ParameterHandler implements ParameterHandlerInterface
{
    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    public function convertOptions(ParameterInterface $parameter)
    {
        return array();
    }

    /**
     * Returns default parameter options for Symfony form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function getDefaultOptions(ParameterInterface $parameter, $parameterName, array $options)
    {
        return array(
            'required' => $parameter->isRequired(),
            'label' => $options['label_prefix'] . '.' . $parameterName,
            'property_path' => $options['property_path_prefix'] . '[' . $parameterName . ']',
        );
    }

    /**
     * Allows the handler to do any kind of processing to created form.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param \Symfony\Component\Form\FormBuilderInterface $form
     */
    public function processForm(ParameterInterface $parameter, FormBuilderInterface $form)
    {
    }
}
