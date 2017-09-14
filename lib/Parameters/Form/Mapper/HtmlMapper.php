<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HtmlMapper extends Mapper
{
    public function getFormType()
    {
        return TextareaType::class;
    }
}
