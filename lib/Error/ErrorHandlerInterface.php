<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Error;

use Throwable;

interface ErrorHandlerInterface
{
    /**
     * Handles the provided throwable. Context can be arbitrary data relevant to the error.
     */
    public function handleError(Throwable $throwable, string $message = null, array $context = []): void;
}
