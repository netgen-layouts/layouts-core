<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Netgen\BlockManager\Collection\Result\Slot;
use PHPUnit\Framework\TestCase;

class SlotTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getValueId
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getValueType
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getName
     * @covers \Netgen\BlockManager\Collection\Result\Slot::isVisible
     * @covers \Netgen\BlockManager\Collection\Result\Slot::getObject
     */
    public function testObject()
    {
        $value = new Slot();

        $this->assertEquals(0, $value->getValueId());
        $this->assertEquals('slot', $value->getValueType());
        $this->assertEquals('(UNKNOWN ITEM)', $value->getName());
        $this->assertTrue($value->isVisible());
        $this->assertNull($value->getObject());
    }
}
