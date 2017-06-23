<?php

namespace Netgen\BlockManager\Tests\Exception\Block;

use Netgen\BlockManager\Exception\Block\BlockTypeException;
use PHPUnit\Framework\TestCase;

class BlockTypeExceptionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockTypeException::noBlockType
     */
    public function testNoBlockType()
    {
        $exception = BlockTypeException::noBlockType('type');

        $this->assertEquals(
            'Block type with "type" identifier does not exist.',
            $exception->getMessage()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Exception\Block\BlockTypeException::noBlockTypeGroup
     */
    public function testNoBlockTypeGroup()
    {
        $exception = BlockTypeException::noBlockTypeGroup('type');

        $this->assertEquals(
            'Block type group with "type" identifier does not exist.',
            $exception->getMessage()
        );
    }
}
