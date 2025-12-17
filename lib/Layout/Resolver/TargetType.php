<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver;

abstract class TargetType implements TargetTypeInterface
{
    public function export(int|string $value): int|string|null
    {
        return $value;
    }

    public function import(int|string|null $value): int|string
    {
        return $value ?? '';
    }
}
