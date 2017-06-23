<?php

namespace Netgen\BlockManager\Tests\Exception\Core;

use Netgen\BlockManager\Exception\Core\BlockException;
use PHPUnit\Framework\TestCase;

class BlockExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Core\BlockException::noPlaceholder
     */
    public function testNoBlock()
    {
        $exception = BlockException::noPlaceholder('placeholder');

        $this->assertEquals(
            'Placeholder with "placeholder" identifier does not exist in the block.',
            $exception->getMessage()
        );
    }
}
