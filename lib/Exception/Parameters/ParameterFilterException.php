<?php

namespace Netgen\BlockManager\Exception\Parameters;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;
use Netgen\BlockManager\Parameters\ParameterFilterInterface;

class ParameterFilterException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $class
     *
     * @return \Netgen\BlockManager\Exception\Parameters\ParameterFilterException
     */
    public static function invalidClass($class)
    {
        return new self(
            sprintf(
                'Parameter filter "%s" needs to implement %s.',
                $class,
                ParameterFilterInterface::class
            )
        );
    }
}
