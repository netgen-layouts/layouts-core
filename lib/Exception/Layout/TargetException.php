<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Layout;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class TargetException extends InvalidArgumentException implements Exception
{
    public static function valueObjectNotSupported(string $targetType): self
    {
        return new self(
            sprintf(
                'Target of type "%s" does not support value objects.',
                $targetType,
            ),
        );
    }
}
