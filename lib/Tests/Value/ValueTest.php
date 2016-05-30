<?php

namespace Netgen\BlockManager\Tests\Value;

use Netgen\BlockManager\Value\Value;
use stdClass;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Value\Value::getValueId
     * @covers \Netgen\BlockManager\Value\Value::getValueType
     * @covers \Netgen\BlockManager\Value\Value::getName
     * @covers \Netgen\BlockManager\Value\Value::isVisible
     * @covers \Netgen\BlockManager\Value\Value::getObject
     */
    public function testObject()
    {
        $value = new Value(
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
