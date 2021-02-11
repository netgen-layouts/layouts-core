<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType;
use Symfony\Component\HttpFoundation\Request;

final class NullTargetType extends TargetType
{
    public static function getType(): string
    {
        return 'null';
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function provideValue(Request $request): ?int
    {
        return null;
    }
}
