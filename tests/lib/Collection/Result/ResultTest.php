<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\Result;
use Netgen\BlockManager\Item\Item;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\Result::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Result::getItem
     * @covers \Netgen\BlockManager\Collection\Result\Result::getPosition
     * @covers \Netgen\BlockManager\Collection\Result\Result::getSubItem
     */
    public function testObject()
    {
        $result = new Result(0, new Item(['value' => 42]), new Item(['value' => 43]));

        $this->assertEquals(0, $result->getPosition());
        $this->assertEquals(new Item(['value' => 42]), $result->getItem());
        $this->assertEquals(new Item(['value' => 43]), $result->getSubItem());
    }
}
