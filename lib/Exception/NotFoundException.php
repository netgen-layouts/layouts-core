<?php

namespace Netgen\BlockManager\Exception;

use Exception as BaseException;

final class NotFoundException extends BaseException implements Exception
{
    /**
     * Creates a new not found exception.
     *
     * @param string $what
     * @param int|string $identifier
     * @param \Exception $previousException
     */
    public function __construct($what, $identifier = '', BaseException $previousException = null)
    {
        $message = !empty($identifier) ?
            sprintf('Could not find %s with identifier "%s"', $what, $identifier) :
            sprintf('Could not find %s', $what);

        parent::__construct($message, 0, $previousException);
    }
}
