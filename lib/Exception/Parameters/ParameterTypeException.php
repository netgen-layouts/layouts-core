<?php

namespace Netgen\BlockManager\Exception\Parameters;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class ParameterTypeException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $parameterType
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterTypeException
     */
    public static function noParameterType($parameterType)
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
    public static function noParameterTypeClass($class)
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
    public static function noFormMapper($parameterType)
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
    public static function unsupportedParameterType($parameterType)
    {
        return new self(
            sprintf(
                'Parameter with "%s" type is not supported.',
                $parameterType
            )
        );
    }
}
