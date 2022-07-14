<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Parameters;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class ParameterTypeException extends InvalidArgumentException implements Exception
{
    public static function noParameterType(string $parameterType): self
    {
        return new self(
            sprintf(
                'Parameter type with "%s" identifier does not exist.',
                $parameterType,
            ),
        );
    }

    public static function noParameterTypeClass(string $class): self
    {
        return new self(
            sprintf(
                'Parameter type with class "%s" does not exist.',
                $class,
            ),
        );
    }

    public static function noFormMapper(string $parameterType): self
    {
        return new self(
            sprintf(
                'Form mapper for "%s" parameter type does not exist.',
                $parameterType,
            ),
        );
    }

    public static function unsupportedParameterType(string $parameterType): self
    {
        return new self(
            sprintf(
                'Parameter with "%s" type is not supported.',
                $parameterType,
            ),
        );
    }
}
