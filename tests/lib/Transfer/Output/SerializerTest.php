<?php

namespace Netgen\BlockManager\Tests\Transfer\Output;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Transfer\Descriptor;
use Netgen\BlockManager\Transfer\Output\Serializer;
use Netgen\BlockManager\Transfer\Output\VisitorInterface;
use PHPUnit\Framework\TestCase;

final class SerializerTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutServiceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverServiceMock;

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
        $this->layoutServiceMock = $this->createMock(LayoutService::class);
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);
        $this->visitorMock = $this->createMock(VisitorInterface::class);

        $this->serializer = new Serializer(
            $this->layoutServiceMock,
            $this->layoutResolverServiceMock,
            $this->visitorMock
        );
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::__construct
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::loadLayouts
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::serializeLayouts
     */
    public function testSerializeLayouts()
    {
        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Layout(['id' => 42])));

        $this->layoutServiceMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->with($this->equalTo(24))
            ->will($this->returnValue(new Layout(['id' => 24])));

        $this->visitorMock
            ->expects($this->at(0))
            ->method('visit')
            ->with($this->equalTo(new Layout(['id' => 42])))
            ->will($this->returnValue('serialized_layout_42'));

        $this->visitorMock
            ->expects($this->at(1))
            ->method('visit')
            ->with($this->equalTo(new Layout(['id' => 24])))
            ->will($this->returnValue('serialized_layout_24'));

        $this->assertEquals(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    'serialized_layout_42',
                    'serialized_layout_24',
                ],
            ],
            $this->serializer->serializeLayouts([42, 24])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::loadLayouts
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::serializeLayouts
     */
    public function testSerializeLayoutsWithNonExistentLayout()
    {
        $this->layoutServiceMock
            ->expects($this->at(0))
            ->method('loadLayout')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Layout(['id' => 42])));

        $this->layoutServiceMock
            ->expects($this->at(1))
            ->method('loadLayout')
            ->with($this->equalTo(24))
            ->will($this->throwException(new NotFoundException('layout', 24)));

        $this->visitorMock
            ->expects($this->at(0))
            ->method('visit')
            ->with($this->equalTo(new Layout(['id' => 42])))
            ->will($this->returnValue('serialized_layout_42'));

        $this->assertEquals(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    'serialized_layout_42',
                ],
            ],
            $this->serializer->serializeLayouts([42, 24])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::loadRules
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::serializeRules
     */
    public function testSerializeRules()
    {
        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Rule(['id' => 42])));

        $this->layoutResolverServiceMock
            ->expects($this->at(1))
            ->method('loadRule')
            ->with($this->equalTo(24))
            ->will($this->returnValue(new Rule(['id' => 24])));

        $this->visitorMock
            ->expects($this->at(0))
            ->method('visit')
            ->with($this->equalTo(new Rule(['id' => 42])))
            ->will($this->returnValue('serialized_rule_42'));

        $this->visitorMock
            ->expects($this->at(1))
            ->method('visit')
            ->with($this->equalTo(new Rule(['id' => 24])))
            ->will($this->returnValue('serialized_rule_24'));

        $this->assertEquals(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    'serialized_rule_42',
                    'serialized_rule_24',
                ],
            ],
            $this->serializer->serializeRules([42, 24])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::loadRules
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::serializeRules
     */
    public function testSerializeRulesWithNonExistentRule()
    {
        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('loadRule')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new Rule(['id' => 42])));

        $this->layoutResolverServiceMock
            ->expects($this->at(1))
            ->method('loadRule')
            ->with($this->equalTo(24))
            ->will($this->throwException(new NotFoundException('rule', 24)));

        $this->visitorMock
            ->expects($this->at(0))
            ->method('visit')
            ->with($this->equalTo(new Rule(['id' => 42])))
            ->will($this->returnValue('serialized_rule_42'));

        $this->assertEquals(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    'serialized_rule_42',
                ],
            ],
            $this->serializer->serializeRules([42, 24])
        );
    }
}
