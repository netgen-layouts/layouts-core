<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Error;

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
    private $debug = false;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param bool $debug
     */
    public function __construct(LoggerInterface $logger = null, $debug = false)
    {
        $this->logger = $logger ?: new NullLogger();
        $this->debug = $debug;
    }

    public function handleError(Throwable $throwable, $message = null, array $context = [])
    {
        $this->logError($throwable, $message, $context);

        if ($this->debug) {
            throw $throwable;
        }
    }

    /**
     * Logs the error.
     *
     * @param \Throwable $throwable
     * @param string $message
     * @param array $context
     */
    private function logError(Throwable $throwable, $message = null, array $context = [])
    {
        $context['error'] = $throwable;

        $this->logger->critical($message ?? $throwable->getMessage(), $context);
    }
}
