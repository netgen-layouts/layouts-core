<?php

namespace Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Mapper;
use Netgen\BlockManager\Layout\Resolver\Form\ConditionType\Type\TimeType;

final class Time extends Mapper
{
    public function getFormType()
    {
        return TimeType::class;
    }
}
