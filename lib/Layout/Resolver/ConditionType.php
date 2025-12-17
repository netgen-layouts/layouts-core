<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

abstract class ConditionType implements ConditionTypeInterface
{
    public function export(int|string|array $value): int|string|array|null
    {
        return $value;
    }

    public function import(int|string|array|null $value): int|string|array
    {
        return $value ?? '';
    }
}
