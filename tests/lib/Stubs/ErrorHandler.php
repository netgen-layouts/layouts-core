<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Stubs;

use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Throwable;

final class ErrorHandler implements ErrorHandlerInterface
{
    private $throw = false;

    public function setThrow(bool $throw = false): void
    {
        $this->throw = $throw;
    }

    public function handleError(Throwable $throwable, $message = null, array $context = []): void
    {
        if ($this->throw) {
            throw $throwable;
        }
    }
}
