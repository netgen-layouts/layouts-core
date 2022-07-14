<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception;

use Exception as BaseException;
use Throwable;

use function sprintf;

final class BadStateException extends BaseException implements Exception
{
    /**
     * Creates a new bad state exception.
     */
    public function __construct(string $argument, string $whatIsWrong, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'Argument "%s" has an invalid state. %s',
                $argument,
                $whatIsWrong,
            ),
            0,
            $previous,
        );
    }
}
