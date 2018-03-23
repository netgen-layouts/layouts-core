<?php

namespace Netgen\BlockManager\Parameters\Form\Mapper;

use Netgen\BlockManager\Form\DateTimeType;
use Netgen\BlockManager\Parameters\Form\Mapper;

final class DateTimeMapper extends Mapper
{
    public function getFormType()
    {
        return DateTimeType::class;
    }
}
