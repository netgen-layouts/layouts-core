<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class Boolean extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
    {
        return CheckboxType::class;
    }
}
