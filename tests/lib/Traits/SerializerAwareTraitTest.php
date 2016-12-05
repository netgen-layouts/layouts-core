<?php

namespace Netgen\BlockManager\Tests\Traits;

use Netgen\BlockManager\Tests\Traits\Stubs\SerializerAwareValue;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerAwareTraitTest extends TestCase
{
    public function testDefaultSerializerValue()
    {
        $value = new SerializerAwareValue();
        $this->assertNull($value->getSerializer());
    }

    /**
     * @covers \Netgen\BlockManager\Traits\SerializerAwareTrait::setSerializer
     */
    public function testSetSerializer()
    {
        $serializer = $this->createMock(SerializerInterface::class);

        $value = new SerializerAwareValue();
        $value->setSerializer($serializer);

        $this->assertEquals($serializer, $value->getSerializer());
    }
}
