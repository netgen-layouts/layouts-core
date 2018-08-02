<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Layout\Resolver\TargetType;

use Netgen\BlockManager\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;

final class NullTargetType implements TargetTypeInterface
{
    public static function getType(): string
    {
        return 'null';
    }

    public function getConstraints(): array
    {
        return [];
    }

    public function provideValue(Request $request)
    {
        return null;
    }
}
