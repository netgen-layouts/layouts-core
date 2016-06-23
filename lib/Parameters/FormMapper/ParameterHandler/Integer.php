<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class Integer extends ParameterHandler
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return IntegerType::class;
    }
}
