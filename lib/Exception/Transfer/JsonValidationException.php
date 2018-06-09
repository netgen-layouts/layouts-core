<?php

namespace Netgen\BlockManager\Exception\Transfer;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class JsonValidationException extends InvalidArgumentException implements Exception
{
    /**
     * Thrown when JSON string could not be parsed.
     *
     * @param string $errorMessage
     * @param int $errorCode
     *
     * @return \Netgen\BlockManager\Exception\Transfer\JsonValidationException
     */
    public static function parseError($errorMessage, $errorCode)
    {
        return new self(
            sprintf(
                'Provided data is not a valid JSON string: %s (error code %d)',
                $errorMessage,
                $errorCode
            )
        );
    }

    /**
     * Thrown when JSON string is not acceptable.
     *
     * @param string $reason
     *
     * @return \Netgen\BlockManager\Exception\Transfer\JsonValidationException
     */
    public static function notAcceptable($reason)
    {
        return new self(
            sprintf(
                'Provided data is not an acceptable JSON string: %s',
                $reason
            )
        );
    }

    /**
     * Thrown when JSON data failed to validate the schema.
     *
     * @param string $errorMessage
     * @param \Throwable $previous
     *
     * @return \Netgen\BlockManager\Exception\Transfer\JsonValidationException
     */
    public static function validationFailed($errorMessage, /* Throwable */ $previous = null)
    {
        return new self(
            sprintf(
                'JSON data failed to validate the schema: %s',
                $errorMessage
            ),
            0,
            $previous
        );
    }
}
