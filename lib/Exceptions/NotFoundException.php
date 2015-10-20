<?php

namespace Netgen\BlockManager\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    /**
     * Creates new not found exception.
     *
     * @param string $what
     * @param int|string $identifier
     * @param \Exception $previousException
     */
    public function __construct($what, $identifier, Exception $previousException = null)
    {
        parent::__construct(
            'Could not find ' . $what . ' with identifier ' . $identifier,
            0,
            $previousException
        );
    }
}
