<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\Value as SerializerValue;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Symfony\Component\HttpFoundation\Response;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\Value
     */
    protected $value;

    public function setUp()
    {
        $this->value = new SerializerValue(new Value(), 42, Response::HTTP_ACCEPTED);
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\SerializerValue::__construct
     * @covers Netgen\BlockManager\Serializer\Values\SerializerValue::getValue
     */
    public function testGetValue()
    {
        self::assertEquals(new Value(), $this->value->getValue());
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\SerializerValue::__construct
     * @covers Netgen\BlockManager\Serializer\Values\SerializerValue::getVersion
     */
    public function testGetVersion()
    {
        self::assertEquals(42, $this->value->getVersion());
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\SerializerValue::__construct
     * @covers Netgen\BlockManager\Serializer\Values\SerializerValue::getStatusCode
     */
    public function testGetStatusCode()
    {
        self::assertEquals(Response::HTTP_ACCEPTED, $this->value->getStatusCode());
    }
}
