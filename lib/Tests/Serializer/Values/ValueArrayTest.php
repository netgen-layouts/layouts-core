<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\ValueArray;
use Netgen\BlockManager\Tests\API\Stubs\Value as StubValue;
use Symfony\Component\HttpFoundation\Response;

class ValueArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\ValueArray
     */
    protected $value;

    public function setUp()
    {
        $this->value = new ValueArray(array(new StubValue()), Response::HTTP_ACCEPTED);
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\ValueArray::__construct
     * @covers Netgen\BlockManager\Serializer\Values\ValueArray::getValue
     */
    public function testGetValue()
    {
        self::assertEquals(array(new StubValue()), $this->value->getValue());
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\ValueArray::__construct
     * @covers Netgen\BlockManager\Serializer\Values\ValueArray::getStatusCode
     */
    public function testGetStatusCode()
    {
        self::assertEquals(Response::HTTP_ACCEPTED, $this->value->getStatusCode());
    }
}
