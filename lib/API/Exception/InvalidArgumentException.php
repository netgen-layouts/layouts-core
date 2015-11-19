<?php

namespace Netgen\BlockManager\API\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;
use Exception;

class InvalidArgumentException extends BaseInvalidArgumentException
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
