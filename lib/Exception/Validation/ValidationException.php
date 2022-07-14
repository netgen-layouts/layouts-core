<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Validation;

use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\Layouts\Exception\Exception;
use Throwable;

use function sprintf;

final class ValidationException extends BaseInvalidArgumentException implements Exception
{
    public static function validationFailed(string $propertyPath, string $message, ?Throwable $previous = null): self
    {
        return new self(
            sprintf(
                'There was an error validating "%s": %s',
                $propertyPath,
                $message,
            ),
            0,
            $previous,
        );
    }
}
