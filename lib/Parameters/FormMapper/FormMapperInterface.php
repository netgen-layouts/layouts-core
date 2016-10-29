<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

interface FormMapperInterface
{
    /**
     * Maps the parameter to form type in provided builder.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \Netgen\BlockManager\Parameters\ParameterDefinitionInterface $parameterDefinition
     * @param string $parameterName
     * @param array $options
     */
    public function mapParameter(
        FormBuilderInterface $formBuilder,
        ParameterDefinitionInterface $parameterDefinition,
        $parameterName,
        array $options = array()
    );
}
