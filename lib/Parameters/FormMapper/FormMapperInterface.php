<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

interface FormMapperInterface
{
    /**
     * Maps the parameter to form type in provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param string $labelPrefix,
     * @param string $propertyPathPrefix
     */
    public function mapParameter(
        FormBuilderInterface $formBuilder,
        ParameterInterface $parameter,
        $parameterName,
        $labelPrefix,
        $propertyPathPrefix = 'parameters'
    );

    /**
     * Maps the parameter to hidden form type in provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     * @param string $parameterName
     * @param string $propertyPathPrefix
     */
    public function mapHiddenParameter(
        FormBuilderInterface $formBuilder,
        ParameterInterface $parameter,
        $parameterName,
        $propertyPathPrefix = 'parameters'
    );
}
