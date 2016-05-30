<?php

namespace Netgen\BlockManager\Tests\Item;

use Netgen\BlockManager\Item\Item;
use stdClass;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Item\Value::getValueId
     * @covers \Netgen\BlockManager\Item\Value::getValueType
     * @covers \Netgen\BlockManager\Item\Value::getName
     * @covers \Netgen\BlockManager\Item\Value::isVisible
     * @covers \Netgen\BlockManager\Item\Value::getObject
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

        self::assertEquals(42, $value->getValueId());
        self::assertEquals('type', $value->getValueType());
        self::assertEquals('Value name', $value->getName());
        self::assertEquals(true, $value->isVisible());
        self::assertEquals(new stdClass(), $value->getObject());
    }
}
