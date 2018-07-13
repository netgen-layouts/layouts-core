<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Parameters;

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

    public static function noParameterDefinition(string $parameter): self
    {
        return new self(
            sprintf(
                'Parameter definition with "%s" name does not exist.',
                $parameter
            )
        );
    }

    public static function noOption(string $option): self
    {
        return new self(
            sprintf(
                'Option "%s" does not exist in the parameter definition.',
                $option
            )
        );
    }
}
