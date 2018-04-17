<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\ParameterDefinitionInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    public function mapOptions(ParameterDefinitionInterface $parameterDefinition)
    {
        return [];
    }

    public function handleForm(FormBuilderInterface $form, ParameterDefinitionInterface $parameterDefinition)
    {
    }
}
