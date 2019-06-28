<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\ConditionType;

use Netgen\Layouts\Layout\Resolver\ConditionTypeInterface;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\ErrorCatcher\Exception\FlattenException as ErrorCatcherFlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;

final class Exception implements ConditionTypeInterface
{
    public static function getType(): string
    {
        return 'exception';
    }

    public function getConstraints(): array
    {
        return [
            new Constraints\NotNull(),
            new Constraints\Type(['type' => 'array']),
            new Constraints\All(
                [
                    'constraints' => [
                        new Constraints\Type(['type' => 'int']),
                        new Constraints\GreaterThanOrEqual(['value' => 400]),
                        new Constraints\LessThan(['value' => 600]),
                    ],
                ]
            ),
        ];
    }

    public function matches(Request $request, $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        if (!$request->attributes->has('exception')) {
            return false;
        }

        $exception = $request->attributes->get('exception');
        if (class_exists(ErrorCatcherFlattenException::class)) {
            if (!$exception instanceof ErrorCatcherFlattenException) {
                return false;
            }
        } elseif (!$exception instanceof DebugFlattenException) {
            return false;
        }

        return count($value) === 0 || in_array($exception->getStatusCode(), $value, true);
    }
}
