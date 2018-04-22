<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\ParameterDefinition;
use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    public function mapOptions(ParameterDefinition $parameterDefinition)
    {
        return [];
    }

    public function handleForm(FormBuilderInterface $form, ParameterDefinition $parameterDefinition)
    {
    }
}
