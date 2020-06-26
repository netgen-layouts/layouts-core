<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\ConditionType;

use Netgen\Layouts\Layout\Resolver\ConditionType;
use Symfony\Component\HttpFoundation\Request;

final class NullConditionType extends ConditionType
{
    public static function getType(): string
    {
        return 'null';
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function matches(Request $request, $value): bool
    {
        return true;
    }
}
