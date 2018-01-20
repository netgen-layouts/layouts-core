<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use PHPUnit\Framework\TestCase;
use stdClass;

final class ItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\Item::getValueId
     * @covers \Netgen\BlockManager\Item\Item::getRemoteId
     * @covers \Netgen\BlockManager\Item\Item::getValueType
     * @covers \Netgen\BlockManager\Item\Item::getName
     * @covers \Netgen\BlockManager\Item\Item::isVisible
     * @covers \Netgen\BlockManager\Item\Item::getObject
     */
    public function testObject()
    {
        $value = new Item(
            array(
                'valueId' => 42,
                'remoteId' => 'abc',
                'valueType' => 'type',
                'name' => 'Value name',
                'isVisible' => true,
                'object' => new stdClass(),
            )
        );

        $this->assertEquals(42, $value->getValueId());
        $this->assertEquals('abc', $value->getRemoteId());
        $this->assertEquals('type', $value->getValueType());
        $this->assertEquals('Value name', $value->getName());
        $this->assertTrue($value->isVisible());
        $this->assertEquals(new stdClass(), $value->getObject());
    }
}
