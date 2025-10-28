<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception;

use Exception as BaseException;
use Throwable;

use function sprintf;

final class NotFoundException extends BaseException implements Exception
{
    /**
     * Creates a new not found exception.
     */
    public function __construct(string $what, int|string $identifier = '', ?Throwable $previous = null)
    {
        $message = $identifier !== '' ?
            sprintf('Could not find %s with identifier "%s"', $what, $identifier) :
            sprintf('Could not find %s', $what);

        parent::__construct($message, 0, $previous);
    }
}
