<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Core;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ParameterException extends InvalidArgumentException implements Exception
{
    public static function noParameter(string $parameter): self
    {
        return new self(
            sprintf(
                'Parameter with "%s" name does not exist.',
                $parameter
            )
        );
    }
}
