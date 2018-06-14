<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Validation;

use Exception as BaseException;
use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ValidationException extends BaseInvalidArgumentException implements Exception
{
    /**
     * @param string $propertyPath
     * @param string $message
     * @param \Exception $previous
     *
     * @return \Netgen\BlockManager\Exception\Validation\ValidationException
     */
    public static function validationFailed(string $propertyPath, string $message, BaseException $previous = null): self
    {
        return new self(
            sprintf(
                'There was an error validating "%s": %s',
                (string) $propertyPath,
                $message
            ),
            0,
            $previous
        );
    }
}
