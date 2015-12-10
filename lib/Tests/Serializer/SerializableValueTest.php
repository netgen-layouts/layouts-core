<?php

namespace Netgen\BlockManager\Tests\Serializer;

use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;

class SerializableValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Netgen\BlockManager\Serializer\SerializableValue::__construct
     * @covers Netgen\BlockManager\Serializer\SerializableValue::getValue
     */
    public function testGetValue()
    {
        $value = new SerializableValue(new Value(), 42);
        self::assertEquals(new Value(), $value->getValue());
    }

    /**
     * @covers Netgen\BlockManager\Serializer\SerializableValue::__construct
     * @covers Netgen\BlockManager\Serializer\SerializableValue::getVersion
     */
    public function testGetVersion()
    {
        $value = new SerializableValue(new Value(), 42);
        self::assertEquals(42, $value->getVersion());
    }
}
