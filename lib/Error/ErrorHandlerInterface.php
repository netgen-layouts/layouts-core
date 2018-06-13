<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Error;

use Throwable;

interface ErrorHandlerInterface
{
    /**
     * Handles the provided throwable. Context can be arbitrary data relevant to the error.
     *
     * @param \Throwable $throwable The throwable that needs to be handled
     * @param string $message
     * @param array $context
     */
    public function handleError(Throwable $throwable, $message = null, array $context = []);
}
