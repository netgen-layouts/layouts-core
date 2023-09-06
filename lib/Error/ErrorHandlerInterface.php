<?php

declare(strict_types=1);

namespace Netgen\Layouts\Error;

use Throwable;

interface ErrorHandlerInterface
{
    /**
     * Handles the provided throwable. Context can be arbitrary data relevant to the error.
     *
     * @param array<string, mixed> $context
     */
    public function handleError(Throwable $throwable, ?string $message = null, array $context = []): void;

    /**
     * Logs the error. Context can be arbitrary data relevant to the error.
     *
     * @param array<string, mixed> $context
     */
    public function logError(Throwable $throwable, ?string $message = null, array $context = []): void;
}
