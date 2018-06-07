<?php

namespace Netgen\BlockManager\Exception\Transfer;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class JsonValidationException extends InvalidArgumentException implements Exception
{
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
