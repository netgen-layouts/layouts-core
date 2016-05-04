<?php

namespace Netgen\BlockManager\Tests\Serializer;

use Netgen\BlockManager\Serializer\SerializableValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;

class SerializableValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\SerializableValue
     */
    protected $value;

    public function setUp()
    {
        $this->value = new SerializableValue(new Value(), 42);
    }

    /**
     * @covers Netgen\BlockManager\Serializer\SerializableValue::__construct
     * @covers Netgen\BlockManager\Serializer\SerializableValue::getValue
     */
    public function testGetValue()
    {
        self::assertEquals(new Value(), $this->value->getValue());
    }

    /**
     * @covers Netgen\BlockManager\Serializer\SerializableValue::__construct
     * @covers Netgen\BlockManager\Serializer\SerializableValue::getVersion
     */
    public function testGetVersion()
    {
        self::assertEquals(42, $this->value->getVersion());
    }
}
