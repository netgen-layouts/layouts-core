<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Layout;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class TargetTypeException extends InvalidArgumentException implements Exception
{
    public static function noTargetType(string $targetType): self
    {
        return new self(
            sprintf(
                'Target type "%s" does not exist.',
                $targetType,
            ),
        );
    }

    public static function noFormMapper(string $targetType): self
    {
        return new self(
            sprintf(
                'Form mapper for "%s" target type does not exist.',
                $targetType,
            ),
        );
    }
}
