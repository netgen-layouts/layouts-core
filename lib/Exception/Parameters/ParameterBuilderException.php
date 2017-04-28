<?php

namespace Netgen\BlockManager\Exception\Parameters;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class ParameterBuilderException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $parameter
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function noParameter($parameter)
    {
        return new self(
            sprintf(
                'Parameter with "%s" name does not exist in the builder.',
                $parameter
            )
        );
    }

    /**
     * @param string $option
     * @param string $parameter
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function noOption($option, $parameter)
    {
        return new self(
            sprintf(
                'Option "%s" does not exist in builder for "%s" parameter',
                $option,
                $parameter
            )
        );
    }

    /**
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function subCompound()
    {
        return new self('Compound parameters cannot be added to compound parameters.');
    }

    /**
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterBuilderException
     */
    public static function nonCompound()
    {
        return new self('Parameters cannot be added to non-compound parameters.');
    }
}
