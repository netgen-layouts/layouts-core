<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RequestUriPrefix extends Mapper
{
    /**
     * Returns the form type that will be used to edit the value of this target type.
     *
     * @return string
     */
    public function getFormType()
    {
        return TextType::class;
    }
}
