<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use stdClass;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\Item::getValueId
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
                'valueType' => 'type',
                'name' => 'Value name',
                'isVisible' => true,
                'object' => new stdClass(),
            )
        );

        $this->assertEquals(42, $value->getValueId());
        $this->assertEquals('type', $value->getValueType());
        $this->assertEquals('Value name', $value->getName());
        $this->assertEquals(true, $value->isVisible());
        $this->assertEquals(new stdClass(), $value->getObject());
    }
}
