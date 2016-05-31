<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class ParameterHandler implements ParameterHandlerInterface
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    abstract protected function getFormType();

    /**
     * Maps the parameter to Symfony form type.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param array $options
     *
     * @return array
     */
    public function mapForm(
        FormBuilderInterface $formBuilder,
        ParameterInterface $parameter,
        $parameterName,
        array $options = array()
    ) {
        $formBuilder->add(
            $parameterName,
            $this->getFormType(),
            $this->convertOptions($parameter) + $this->getDefaultOptions(
                $parameter, $parameterName, $options
            )
        );
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    protected function convertOptions(ParameterInterface $parameter)
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
    protected function getDefaultOptions(ParameterInterface $parameter, $parameterName, array $options)
    {
        return array(
            'required' => $parameter->isRequired(),
            'label' => $options['label_prefix'] . '.' . $parameterName,
            'property_path' => $this->getPropertyPath($options['property_path_prefix'], $parameterName),
            'constraints' => $parameter->getConstraints($options['validation_groups']),
        );
    }

    /**
     * Returns the property path based on parameter name and prefix.
     *
     * @param string $propertyPathPrefix
     * @param string $parameterName
     *
     * @return string
     */
    protected function getPropertyPath($propertyPathPrefix, $parameterName)
    {
        if (empty($propertyPathPrefix)) {
            return $parameterName;
        }

        return $propertyPathPrefix . '[' . $parameterName . ']';
    }
}
