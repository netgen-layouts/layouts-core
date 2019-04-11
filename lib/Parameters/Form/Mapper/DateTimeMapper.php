<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters\Form\Mapper;

use Netgen\Layouts\Form\DateTimeType;
use Netgen\Layouts\Parameters\Form\Mapper;

final class DateTimeMapper extends Mapper
{
    public function getFormType(): string
    {
        return DateTimeType::class;
    }
}
