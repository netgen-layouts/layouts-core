<?php

namespace Netgen\BlockManager\Tests\Exception\Core;

use Netgen\BlockManager\Exception\Core\BlockException;
use PHPUnit\Framework\TestCase;

final class BlockExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Core\BlockException::noPlaceholder
     */
    public function testNoPlaceholder()
    {
        $exception = BlockException::noPlaceholder('placeholder');

        $this->assertEquals(
            'Placeholder with "placeholder" identifier does not exist in the block.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Core\BlockException::noCollection
     */
    public function testNoCollection()
    {
        $exception = BlockException::noCollection('collection');

        $this->assertEquals(
            'Collection with "collection" identifier does not exist in the block.',
            $exception->getMessage()
        );
    }
}
