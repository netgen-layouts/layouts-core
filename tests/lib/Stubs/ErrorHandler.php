<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Stubs;

use Netgen\BlockManager\Error\ErrorHandlerInterface;

final class ErrorHandler implements ErrorHandlerInterface
{
    private $throw = false;

    public function setThrow($throw = false)
    {
        $this->throw = $throw;
    }

    public function handleError(/* Throwable */ $throwable, $message = null, array $context = [])
    {
        if ($this->throw) {
            throw $throwable;
        }
    }
}
