<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;

use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Mapper;
use Netgen\Layouts\Layout\Resolver\Form\ConditionType\Type\TimeType;

final class Time extends Mapper
{
    public function getFormType(): string
    {
        return TimeType::class;
    }

    public function getFormOptions(): array
    {
        return [
            'label' => false,
        ];
    }
}
