<?php

namespace Netgen\BlockManager\Exception;

use RuntimeException as BaseRuntimeException;

class InvalidInterfaceException extends BaseRuntimeException implements Exception
{
    public function __construct($what, $identifier, $interface)
    {
        parent::__construct(
            sprintf(
                '%s "%s" needs to implement "%s" interface.',
                $what,
                $identifier,
                $interface
            )
        );
    }
}
