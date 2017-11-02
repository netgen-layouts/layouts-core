<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class TextMapper extends Mapper
{
    public function getFormType()
    {
        return TextareaType::class;
    }
}
