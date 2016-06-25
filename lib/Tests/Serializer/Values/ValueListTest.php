<?php

namespace Netgen\BlockManager\Tests\Serializer\Values;

use Netgen\BlockManager\Serializer\Values\ValueList;
use Netgen\BlockManager\Tests\Core\Stubs\Value as StubValue;
use Symfony\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;

class ValueListTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Serializer\Values\ValueList
     */
    protected $value;

    public function setUp()
    {
        $this->value = new ValueList(array(new StubValue()), Response::HTTP_ACCEPTED);
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\AbstractValue::__construct
     * @covers Netgen\BlockManager\Serializer\Values\ValueList::getValue
     */
    public function testGetValue()
    {
        self::assertEquals(array(new StubValue()), $this->value->getValue());
    }

    /**
     * @covers Netgen\BlockManager\Serializer\Values\ValueList::__construct
     * @covers Netgen\BlockManager\Serializer\Values\ValueList::getStatusCode
     */
    public function testGetStatusCode()
    {
        self::assertEquals(Response::HTTP_ACCEPTED, $this->value->getStatusCode());
    }
}
