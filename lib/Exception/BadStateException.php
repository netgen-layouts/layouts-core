<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Exception;

use Exception as BaseException;

final class BadStateException extends BaseException implements Exception
{
    /**
     * Creates a new bad state exception.
     */
    public function __construct(string $argument, string $whatIsWrong, BaseException $previousException = null)
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
