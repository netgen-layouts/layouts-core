<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class BooleanMapper extends Mapper
{
    public function getFormType()
    {
        return CheckboxType::class;
    }
}
