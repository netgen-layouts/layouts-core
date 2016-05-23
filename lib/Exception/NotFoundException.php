<?php

namespace Netgen\BlockManager\Exception;

use Exception as BaseException;

class NotFoundException extends Exception
{
    /**
     * Creates a new not found exception.
     *
     * @param string $what
     * @param int|string $identifier
     * @param \Exception $previousException
     */
    public function __construct($what, $identifier, BaseException $previousException = null)
    {
        parent::__construct(
            'Could not find ' . $what . ' with identifier "' . $identifier . '"',
            0,
            $previousException
        );
    }
}
