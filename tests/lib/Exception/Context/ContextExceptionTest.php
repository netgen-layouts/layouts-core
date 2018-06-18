<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Exception\Context;

use Netgen\BlockManager\Exception\Context\ContextException;
use PHPUnit\Framework\TestCase;

final class ContextExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Context\ContextException::noVariable
     */
    public function testNoVariable(): void
    {
        $exception = ContextException::noVariable('var');

        $this->assertSame(
            'Variable "var" does not exist in the context.',
            $exception->getMessage()
        );
    }
}
