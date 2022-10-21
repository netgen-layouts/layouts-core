<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Parameters;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class ParameterException extends InvalidArgumentException implements Exception
{
    public static function noParameter(string $parameter): self
    {
        return new self(
            sprintf(
                'Parameter with "%s" name does not exist.',
                $parameter,
            ),
        );
    }

    public static function noParameterDefinition(string $parameter): self
    {
        return new self(
            sprintf(
                'Parameter definition with "%s" name does not exist.',
                $parameter,
            ),
        );
    }

    public static function noOption(string $option): self
    {
        return new self(
            sprintf(
                'Option "%s" does not exist in the parameter definition.',
                $option,
            ),
        );
    }

    public static function valueObjectNotSupported(string $parameter, string $parameterType): self
    {
        return new self(
            sprintf(
                'Parameter "%s" of type "%s" does not support value objects.',
                $parameter,
                $parameterType,
            ),
        );
    }
}
