<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Error;

interface ErrorHandlerInterface
{
    /**
     * Handles the provided throwable. Context can be arbitrary data relevant to the error.
     *
     * @param \Throwable $throwable The throwable that needs to be handled
     * @param string $message
     * @param array $context
     *
     * @todo Add \Throwable type hint when support for PHP 5.6 ends.
     */
    public function handleError(/* Throwable */ $throwable, $message = null, array $context = []);
}
