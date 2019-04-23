<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Transfer\Descriptor;
use Netgen\Layouts\Transfer\Output\Serializer;
use Netgen\Layouts\Transfer\Output\VisitorInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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
     * @var \Netgen\Layouts\Transfer\Output\Serializer
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
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::__construct
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::loadLayouts
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::serializeLayouts
     */
    public function testSerializeLayouts(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $layout1 = Layout::fromArray(['id' => $uuid1]);
        $layout2 = Layout::fromArray(['id' => $uuid2]);

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->with(self::equalTo($uuid1))
            ->willReturn($layout1);

        $this->layoutServiceMock
            ->expects(self::at(1))
            ->method('loadLayout')
            ->with(self::equalTo($uuid2))
            ->willReturn($layout2);

        $this->visitorMock
            ->expects(self::at(0))
            ->method('visit')
            ->with(self::identicalTo($layout1))
            ->willReturn('serialized_layout_1');

        $this->visitorMock
            ->expects(self::at(1))
            ->method('visit')
            ->with(self::identicalTo($layout2))
            ->willReturn('serialized_layout_2');

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    'serialized_layout_1',
                    'serialized_layout_2',
                ],
            ],
            $this->serializer->serializeLayouts([$uuid1->toString(), $uuid2->toString()])
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::loadLayouts
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::serializeLayouts
     */
    public function testSerializeLayoutsWithNonExistentLayout(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $layout = Layout::fromArray(['id' => $uuid2]);

        $this->layoutServiceMock
            ->expects(self::at(0))
            ->method('loadLayout')
            ->with(self::equalTo($uuid1))
            ->willThrowException(new NotFoundException('layout', $uuid1->toString()));

        $this->layoutServiceMock
            ->expects(self::at(1))
            ->method('loadLayout')
            ->with(self::equalTo($uuid2))
            ->willReturn($layout);

        $this->visitorMock
            ->expects(self::at(0))
            ->method('visit')
            ->with(self::identicalTo($layout))
            ->willReturn('serialized_layout_2');

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    'serialized_layout_2',
                ],
            ],
            $this->serializer->serializeLayouts([$uuid1->toString(), $uuid2->toString()])
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::loadRules
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::serializeRules
     */
    public function testSerializeRules(): void
    {
        $rule1 = Rule::fromArray(['id' => 42]);
        $rule2 = Rule::fromArray(['id' => 24]);

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('loadRule')
            ->with(self::identicalTo(42))
            ->willReturn($rule1);

        $this->layoutResolverServiceMock
            ->expects(self::at(1))
            ->method('loadRule')
            ->with(self::identicalTo(24))
            ->willReturn($rule2);

        $this->visitorMock
            ->expects(self::at(0))
            ->method('visit')
            ->with(self::identicalTo($rule1))
            ->willReturn('serialized_rule_42');

        $this->visitorMock
            ->expects(self::at(1))
            ->method('visit')
            ->with(self::identicalTo($rule2))
            ->willReturn('serialized_rule_24');

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
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::loadRules
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::serializeRules
     */
    public function testSerializeRulesWithNonExistentRule(): void
    {
        $rule = Rule::fromArray(['id' => 42]);

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('loadRule')
            ->with(self::identicalTo(24))
            ->willThrowException(new NotFoundException('rule', 24));

        $this->layoutResolverServiceMock
            ->expects(self::at(1))
            ->method('loadRule')
            ->with(self::identicalTo(42))
            ->willReturn($rule);

        $this->visitorMock
            ->expects(self::at(0))
            ->method('visit')
            ->with(self::identicalTo($rule))
            ->willReturn('serialized_rule_42');

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
