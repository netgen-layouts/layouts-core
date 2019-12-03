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
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug;

    public function __construct(?LoggerInterface $logger = null, bool $debug = false)
    {
        $this->logger = $logger ?? new NullLogger();
        $this->debug = $debug;
    }

    public function handleError(Throwable $throwable, ?string $message = null, array $context = []): void
    {
        $this->logError($throwable, $message, $context);

        if ($this->debug) {
            throw $throwable;
        }
    }

    /**
     * Logs the error.
     *
     * @param array<string, mixed> $context
     */
    private function logError(Throwable $throwable, ?string $message = null, array $context = []): void
    {
        $context['error'] = $throwable;

        $this->logger->critical($message ?? $throwable->getMessage(), $context);
    }
}
