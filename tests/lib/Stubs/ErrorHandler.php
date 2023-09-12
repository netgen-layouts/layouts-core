<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Stubs;

use Netgen\Layouts\Error\ErrorHandlerInterface;
use Throwable;

final class ErrorHandler implements ErrorHandlerInterface
{
    private bool $throw = false;

    public function setThrow(bool $throw = false): void
    {
        $this->throw = $throw;
    }

    public function handleError(Throwable $throwable, ?string $message = null, array $context = []): void
    {
        if ($this->throw) {
            throw $throwable;
        }
    }

    public function logError(Throwable $throwable, ?string $message = null, array $context = []): void {}
}
