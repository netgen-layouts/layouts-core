<?php

namespace Netgen\BlockManager\Exception\Parameters;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ParameterException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $parameter
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterException
     */
    public static function noParameterDefinition($parameter)
    {
        return new self(
            sprintf(
                'Parameter definition with "%s" name does not exist in the object.',
                $parameter
            )
        );
    }

    /**
     * @param string $option
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterException
     */
    public static function noOption($option)
    {
        return new self(
            sprintf(
                'Option "%s" does not exist in the parameter definition.',
                $option
            )
        );
    }
}
