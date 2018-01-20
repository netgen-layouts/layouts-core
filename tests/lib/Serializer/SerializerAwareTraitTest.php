<?php

namespace Netgen\BlockManager\Tests\Serializer;

use Netgen\BlockManager\Tests\Serializer\Stubs\SerializerAwareValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerAwareTraitTest extends TestCase
{
    public function testDefaultSerializerValue()
    {
        $value = new SerializerAwareValue();
        $this->assertNull($value->getSerializer());
    }

    /**
     * @covers \Netgen\BlockManager\Serializer\SerializerAwareTrait::setSerializer
     */
    public function testSetSerializer()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $value = new SerializerAwareValue();
        $value->setSerializer($serializer);

        $this->assertEquals($serializer, $value->getSerializer());
    }
}
