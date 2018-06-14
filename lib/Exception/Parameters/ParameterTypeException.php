<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception\Parameters;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ParameterTypeException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $parameterType
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     */
    public static function noParameterType(string $parameterType): self
    {
        return new self(
            sprintf(
                'Parameter type with "%s" identifier does not exist.',
                $parameterType
            )
        );
    }

    /**
     * @param string $class
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     */
    public static function noParameterTypeClass(string $class): self
    {
        return new self(
            sprintf(
                'Parameter type with class "%s" does not exist.',
                $class
            )
        );
    }

    /**
     * @param string $parameterType
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     */
    public static function noFormMapper(string $parameterType): self
    {
        return new self(
            sprintf(
                'Form mapper for "%s" parameter type does not exist.',
                $parameterType
            )
        );
    }

    /**
     * @param string $parameterType
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     */
    public static function unsupportedParameterType(string $parameterType): self
    {
        return new self(
            sprintf(
                'Parameter with "%s" type is not supported.',
                $parameterType
            )
        );
    }
}
