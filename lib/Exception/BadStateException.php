<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception;

use Exception as BaseException;

final class BadStateException extends BaseException implements Exception
{
    /**
     * Creates a new bad state exception.
     *
     * @param string $argument
     * @param string $whatIsWrong
     * @param \Exception $previousException
     */
    public function __construct($argument, $whatIsWrong, BaseException $previousException = null)
    {
        parent::__construct(
            sprintf(
                'Argument "%s" has an invalid state. %s',
                $argument,
                $whatIsWrong
            ),
            0,
            $previousException
        );
    }
}
