<?php

namespace Netgen\BlockManager\Tests\Stubs;

use Netgen\BlockManager\Error\ErrorHandlerInterface;

final class ErrorHandler implements ErrorHandlerInterface
{
    private $throw = false;

    public function setThrow($throw = false)
    {
        $this->throw = $throw;
    }

    public function handleError(/* Throwable */ $throwable, $message = null, array $context = array())
    {
        if ($this->throw) {
            throw $throwable;
        }
    }
}
