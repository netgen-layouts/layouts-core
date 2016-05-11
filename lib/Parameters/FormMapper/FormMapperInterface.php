<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Form\FormBuilderInterface;

interface FormMapperInterface
{
    /**
     * Maps the parameter to form type in provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\Parameter $parameter
     * @param string $parameterName
     * @param string $labelPrefix,
     * @param \Symfony\Component\Validator\Constraint[] $constraints
     * @param string $propertyPathPrefix
     */
    public function mapParameter(
        FormBuilderInterface $formBuilder,
        Parameter $parameter,
        $parameterName,
        $labelPrefix,
        array $constraints = null,
        $propertyPathPrefix = 'parameters'
    );

    /**
     * Maps the parameter to hidden form type in provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\Parameter $parameter
     * @param string $parameterName
     * @param array $constraints
     * @param string $propertyPathPrefix
     */
    public function mapHiddenParameter(
        FormBuilderInterface $formBuilder,
        Parameter $parameter,
        $parameterName,
        array $constraints = null,
        $propertyPathPrefix = 'parameters'
    );
}
