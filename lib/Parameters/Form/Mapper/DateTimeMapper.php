<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Parameters\Form\Mapper;
use Netgen\BlockManager\Parameters\Form\Type\DateTimeType;

final class DateTimeMapper extends Mapper
{
    public function getFormType()
    {
        return DateTimeType::class;
    }
}
