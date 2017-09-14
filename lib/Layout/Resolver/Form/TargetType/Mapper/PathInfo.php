<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\TargetType\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PathInfo extends Mapper
{
    public function getFormType()
    {
        return TextType::class;
    }
}
