<?php

declare(strict_types=1);

namespace Netgen\Layouts\Layout\Resolver\ConditionType;

use Netgen\Layouts\Layout\Resolver\ConditionType;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\ErrorHandler\Exception\FlattenException as ErrorHandlerFlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Constraints;

use function class_exists;
use function count;
use function in_array;
use function is_array;

final class Exception extends ConditionType
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
                ],
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

        if (!$exception instanceof HttpExceptionInterface) {
            if (class_exists(ErrorHandlerFlattenException::class)) {
                if (!$exception instanceof ErrorHandlerFlattenException) {
                    return false;
                }
            } elseif (class_exists(DebugFlattenException::class)) {
                if (!$exception instanceof DebugFlattenException) {
                    return false;
                }
            }
        }

        return count($value) === 0 || in_array((int) $exception->getStatusCode(), $value, true);
    }
}
