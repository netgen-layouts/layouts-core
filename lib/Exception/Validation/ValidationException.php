<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Validation;

use Exception as BaseException;
use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ValidationException extends BaseInvalidArgumentException implements Exception
{
    public static function validationFailed(string $propertyPath, string $message, BaseException $previous = null): self
    {
        return new self(
            sprintf(
                'There was an error validating "%s": %s',
                $propertyPath,
                $message
            ),
            0,
            $previous
        );
    }
}
