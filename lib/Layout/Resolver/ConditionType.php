<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

abstract class ConditionType implements ConditionTypeInterface
{
    public function export(mixed $value): mixed
    {
        return $value;
    }

    public function import(mixed $value): mixed
    {
        return $value;
    }
}
