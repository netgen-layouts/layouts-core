<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Transfer;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;
use Throwable;

use function sprintf;

final class JsonValidationException extends InvalidArgumentException implements Exception
{
    /**
     * @param int|string $errorCode
     */
    public static function parseError(string $errorMessage, $errorCode): self
    {
        return new self(
            sprintf(
                'Provided data is not a valid JSON string: %s (error code %s)',
                $errorMessage,
                $errorCode,
            ),
        );
    }

    public static function notAcceptable(string $reason): self
    {
        return new self(
            sprintf(
                'Provided data is not an acceptable JSON string: %s',
                $reason,
            ),
        );
    }

    public static function validationFailed(string $errorMessage, ?Throwable $previous = null): self
    {
        return new self(
            sprintf(
                'JSON data failed to validate the schema: %s',
                $errorMessage,
            ),
            0,
            $previous,
        );
    }
}
