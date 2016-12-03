<?php

namespace Netgen\BlockManager\Tests\Parameters\Stubs;

use Netgen\BlockManager\Parameters\Form\Mapper as BaseMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FormMapper extends BaseMapper
{
    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    public function getFormType()
    {
        return TextType::class;
    }
}
