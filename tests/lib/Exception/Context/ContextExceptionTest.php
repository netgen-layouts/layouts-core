<?php

namespace Netgen\BlockManager\Tests\Exception\Collection;

use Netgen\BlockManager\Exception\Context\ContextException;
use PHPUnit\Framework\TestCase;

class ContextExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Context\ContextException::noVariable
     */
    public function testNoVariable()
    {
        $exception = ContextException::noVariable('var');

        $this->assertEquals(
            'Variable "var" does not exist in the context.',
            $exception->getMessage()
        );
    }
}
