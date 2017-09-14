<?php

namespace Netgen\BlockManager\Parameters\Form;

use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\FormBuilderInterface;

abstract class Mapper implements MapperInterface
{
    public function mapOptions(ParameterInterface $parameter)
    {
        return array();
    }

    public function handleForm(FormBuilderInterface $form, ParameterInterface $parameter)
    {
    }
}
