<?php

declare(strict_types=1);

namespace Netgen\Layouts\Error;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Throwable;

/**
 * This error handler rethrows the error if debug mode is on.
 */
final class DebugErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private LoggerInterface $logger = new NullLogger(),
        private bool $debug = false,
    ) {}

    public function handleError(Throwable $throwable, ?string $message = null, array $context = []): void
    {
        $this->logError($throwable, $message, $context);

        if ($this->debug) {
            throw $throwable;
        }
    }

    public function logError(Throwable $throwable, ?string $message = null, array $context = []): void
    {
        $context['error'] = $throwable;

        $this->logger->critical($message ?? $throwable->getMessage(), $context);
    }
}
