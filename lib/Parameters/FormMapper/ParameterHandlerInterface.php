<?php

namespace Netgen\BlockManager\Parameters\FormMapper;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

interface ParameterHandlerInterface
{
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
    );
}
