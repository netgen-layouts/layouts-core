<?php

namespace Netgen\BlockManager\Exceptions;

use Exception;

class InvalidArgumentException extends Exception
{
    /**
     * Creates a new invalid argument exception.
     *
     * @param string $argument
     * @param mixed $value
     * @param string $whatIsWrong
     * @param \Exception $previousException
     */
    public function __construct($argument, $value, $whatIsWrong, Exception $previousException = null)
    {
        parent::__construct(
            'Argument ' . $argument . ' has an invalid value: ' . (string)$value . '. ' . $whatIsWrong,
            0,
            $previousException
        );
    }
}
