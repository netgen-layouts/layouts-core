<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\TargetType;

use Netgen\Layouts\Layout\Resolver\TargetTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class RoutePrefix implements TargetTypeInterface
{
    public static function getType(): string
    {
        return 'route_prefix';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotBlank(),
            new Constraints\Type(['type' => 'string']),
        ];
    }

    public function provideValue(Request $request)
    {
        return $request->attributes->get('_route');
    }
}
