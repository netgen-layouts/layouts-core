<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Identifier extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
    {
        return TextType::class;
    }
}
