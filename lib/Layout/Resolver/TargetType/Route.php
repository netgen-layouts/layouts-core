<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Route extends TargetType
{
    public static function getType(): string
    {
        return 'route';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'string']),
        ];
    }

    public function provideValue(Request $request): ?string
    {
        return $request->attributes->get('_route');
    }
}
