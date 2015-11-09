<?php

namespace Netgen\BlockManager\Serializer\Tests;

use Netgen\BlockManager\API\Tests\Stubs\Value;
use Netgen\BlockManager\Serializer\Tests\Stubs\Serializer;
use PHPUnit_Framework_TestCase;

class SerializerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Serializer\Serializer::serialize
     */
    public function testSerialize()
    {
        $value = new Value(
            array(
                'someProperty' => 42,
                'someOtherProperty' => 24,
            )
        );

        $serializedValue = array(
            'some_property' => 42,
            'some_other_property' => 24,
        );

        $visitorInterfaceMock = $this->getMock('JMS\Serializer\VisitorInterface');
        $visitorInterfaceMock
            ->expects($this->once())
            ->method('visitArray')
            ->with($this->equalTo($serializedValue))
            ->will($this->returnValue($serializedValue));

        $serializer = new Serializer();
        $returnedSerializedValue = $serializer->serialize(
            $visitorInterfaceMock,
            $value,
            array(),
            $this->getMock('JMS\Serializer\SerializationContext')
        );

        self::assertEquals($serializedValue, $returnedSerializedValue);
    }
}
