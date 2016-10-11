<?php

namespace Netgen\BlockManager\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;
use Exception as BaseException;

class InvalidArgumentException extends BaseInvalidArgumentException implements Exception
{
    /**
     * Creates a new invalid argument exception.
     *
     * @param string $argument
     * @param string $whatIsWrong
     * @param \Exception $previousException
     */
    public function __construct($argument, $whatIsWrong, BaseException $previousException = null)
    {
        parent::__construct(
            sprintf(
                'Argument "%s" has an invalid value. %s',
                $argument,
                $whatIsWrong
            ),
            0,
            $previousException
        );
    }
}
