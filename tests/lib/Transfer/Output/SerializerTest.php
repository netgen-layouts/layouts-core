<?php

namespace Netgen\BlockManager\Tests\Transfer\Output;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Transfer\Output\Serializer;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;
use PHPUnit\Framework\TestCase;

final class SerializerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $visitorMock;

    /**
     * @var \Netgen\BlockManager\Transfer\Output\Serializer
     */
    private $serializer;

    public function setUp()
    {
        $this->visitorMock = $this->createMock(VisitorInterface::class);

        $this->serializer = new Serializer($this->visitorMock);
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::__construct
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::serializeLayout
     */
    public function testSerializeLayout()
    {
        $this->visitorMock
            ->expects($this->once())
            ->method('visit')
            ->with($this->equalTo(new Layout()))
            ->will($this->returnValue('serialized_layout'));

        $this->assertEquals('serialized_layout', $this->serializer->serializeLayout(new Layout()));
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::__construct
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::serializeRule
     */
    public function testSerializeRule()
    {
        $this->visitorMock
            ->expects($this->once())
            ->method('visit')
            ->with($this->equalTo(new Rule()))
            ->will($this->returnValue('serialized_rule'));

        $this->assertEquals('serialized_rule', $this->serializer->serializeRule(new Rule()));
    }
}
