<?php

namespace Netgen\BlockManager\Exception\Validation;

use Exception as BaseException;
use InvalidArgumentException as BaseInvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class ValidationFailedException extends BaseInvalidArgumentException implements Exception
{
    public function __construct($propertyPath, $message, BaseException $previous = null)
    {
        parent::__construct(
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
