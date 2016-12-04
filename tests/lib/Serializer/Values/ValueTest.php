<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\Value;
use Netgen\BlockManager\Tests\Core\Stubs\Value as StubValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ValueTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\Value
     */
    protected $value;

    public function setUp()
    {
        $this->value = new Value(new StubValue(), Response::HTTP_ACCEPTED);
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\AbstractValue::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\Value::getValue
     */
    public function testGetValue()
    {
        $this->assertEquals(new StubValue(), $this->value->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\Values\Value::__construct
     * @covers \Netgen\BlockManager\Serializer\Values\Value::getStatusCode
     */
    public function testGetStatusCode()
    {
        $this->assertEquals(Response::HTTP_ACCEPTED, $this->value->getStatusCode());
    }
}
