<?php

namespace Netgen\BlockManager\Tests\Value;

use Netgen\BlockManager\Value\Value;
use stdClass;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Value\Value::getId
     * @covers \Netgen\BlockManager\Value\Value::getType
     * @covers \Netgen\BlockManager\Value\Value::getName
     * @covers \Netgen\BlockManager\Value\Value::isVisible
     * @covers \Netgen\BlockManager\Value\Value::getObject
     */
    public function testObject()
    {
        $value = new Value(
            array(
                'id' => 42,
                'type' => 'type',
                'name' => 'Value name',
                'isVisible' => true,
                'object' => new stdClass(),
            )
        );

        self::assertEquals(42, $value->getId());
        self::assertEquals('type', $value->getType());
        self::assertEquals('Value name', $value->getName());
        self::assertEquals(true, $value->isVisible());
        self::assertEquals(new stdClass(), $value->getObject());
    }
}
