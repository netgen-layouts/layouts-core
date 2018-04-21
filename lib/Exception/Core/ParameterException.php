<?php

namespace Netgen\BlockManager\Exception\Core;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

final class ParameterException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $parameter
     *
     * @return \Netgen\BlockManager\Exception\Core\ParameterException
     */
    public static function noParameter($parameter)
    {
        return new self(
            sprintf(
                'Parameter with "%s" name does not exist.',
                $parameter
            )
        );
    }
}
