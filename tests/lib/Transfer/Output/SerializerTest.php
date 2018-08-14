<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Transfer\Output;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Service\LayoutService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
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

    public function setUp(): void
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
    public function testSerializeLayouts(): void
    {
        $layout1 = Layout::fromArray(['id' => 42]);
        $layout2 = Layout::fromArray(['id' => 24]);

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($layout1));

        $this->layoutServiceMock
            ->expects(self::at(1))
            ->method('loadLayout')
            ->with(self::identicalTo(24))
            ->will(self::returnValue($layout2));

        $this->visitorMock
            ->expects(self::at(0))
            ->method('visit')
            ->with(self::identicalTo($layout1))
            ->will(self::returnValue('serialized_layout_42'));

        $this->visitorMock
            ->expects(self::at(1))
            ->method('visit')
            ->with(self::identicalTo($layout2))
            ->will(self::returnValue('serialized_layout_24'));

        self::assertSame(
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
    public function testSerializeLayoutsWithNonExistentLayout(): void
    {
        $layout = Layout::fromArray(['id' => 42]);

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->with(self::identicalTo(24))
            ->will(self::throwException(new NotFoundException('layout', 24)));

        $this->layoutServiceMock
            ->expects(self::at(1))
            ->method('loadLayout')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($layout));

        $this->visitorMock
            ->expects(self::at(0))
            ->method('visit')
            ->with(self::identicalTo($layout))
            ->will(self::returnValue('serialized_layout_42'));

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    'serialized_layout_42',
                ],
            ],
            $this->serializer->serializeLayouts([24, 42])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::loadRules
     * @covers \Netgen\BlockManager\Transfer\Output\Serializer::serializeRules
     */
    public function testSerializeRules(): void
    {
        $rule1 = Rule::fromArray(['id' => 42]);
        $rule2 = Rule::fromArray(['id' => 24]);

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('loadRule')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($rule1));

        $this->layoutResolverServiceMock
            ->expects(self::at(1))
            ->method('loadRule')
            ->with(self::identicalTo(24))
            ->will(self::returnValue($rule2));

        $this->visitorMock
            ->expects(self::at(0))
            ->method('visit')
            ->with(self::identicalTo($rule1))
            ->will(self::returnValue('serialized_rule_42'));

        $this->visitorMock
            ->expects(self::at(1))
            ->method('visit')
            ->with(self::identicalTo($rule2))
            ->will(self::returnValue('serialized_rule_24'));

        self::assertSame(
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
    public function testSerializeRulesWithNonExistentRule(): void
    {
        $rule = Rule::fromArray(['id' => 42]);

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('loadRule')
            ->with(self::identicalTo(24))
            ->will(self::throwException(new NotFoundException('rule', 24)));

        $this->layoutResolverServiceMock
            ->expects(self::at(1))
            ->method('loadRule')
            ->with(self::identicalTo(42))
            ->will(self::returnValue($rule));

        $this->visitorMock
            ->expects(self::at(0))
            ->method('visit')
            ->with(self::identicalTo($rule))
            ->will(self::returnValue('serialized_rule_42'));

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    'serialized_rule_42',
                ],
            ],
            $this->serializer->serializeRules([24, 42])
        );
    }
}
