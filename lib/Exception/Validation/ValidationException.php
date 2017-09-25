<?php

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
    public static function validationFailed($propertyPath, $message, BaseException $previous = null)
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
