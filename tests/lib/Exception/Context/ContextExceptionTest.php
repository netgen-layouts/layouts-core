<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Exception\Context;

use Netgen\Layouts\Exception\Context\ContextException;
use PHPUnit\Framework\TestCase;

final class ContextExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\Exception\Context\ContextException::noVariable
     */
    public function testNoVariable(): void
    {
        $exception = ContextException::noVariable('var');

        self::assertSame(
            'Variable "var" does not exist in the context.',
            $exception->getMessage(),
        );
    }
}
