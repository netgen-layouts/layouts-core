<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Transfer\Output;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Tests\Transfer\Output\Visitor\Stubs\VisitorStub;
use Netgen\Layouts\Transfer\Descriptor;
use Netgen\Layouts\Transfer\Output\OutputVisitor;
use Netgen\Layouts\Transfer\Output\Serializer;
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
     * @var \Netgen\Layouts\Transfer\Output\Serializer
     */
    private $serializer;

    protected function setUp(): void
    {
        $this->layoutServiceMock = $this->createMock(LayoutService::class);
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->serializer = new Serializer(
            $this->layoutServiceMock,
            $this->layoutResolverServiceMock,
            new OutputVisitor([new VisitorStub()])
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
            ->method('loadLayout')
            ->withConsecutive(
                [self::equalTo($uuid1)],
                [self::equalTo($uuid2)]
            )
            ->willReturnOnConsecutiveCalls($layout1, $layout2);

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    ['visited_value'],
                    ['visited_value'],
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
            ->method('loadLayout')
            ->withConsecutive(
                [self::equalTo($uuid1)],
                [self::equalTo($uuid2)]
            )
            ->willReturnOnConsecutiveCalls(
                self::throwException(new NotFoundException('layout', $uuid1->toString())),
                self::returnValue($layout)
            );

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    ['visited_value'],
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
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $rule1 = Rule::fromArray(['id' => $uuid1]);
        $rule2 = Rule::fromArray(['id' => $uuid2]);

        $this->layoutResolverServiceMock
            ->method('loadRule')
            ->withConsecutive(
                [self::equalTo($uuid1)],
                [self::equalTo($uuid2)]
            )
            ->willReturnOnConsecutiveCalls($rule1, $rule2);

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    ['visited_value'],
                    ['visited_value'],
                ],
            ],
            $this->serializer->serializeRules([$uuid1->toString(), $uuid2->toString()])
        );
    }

    /**
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::createBasicData
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::loadRules
     * @covers \Netgen\Layouts\Transfer\Output\Serializer::serializeRules
     */
    public function testSerializeRulesWithNonExistentRule(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $rule = Rule::fromArray(['id' => $uuid2]);

        $this->layoutResolverServiceMock
            ->method('loadRule')
            ->withConsecutive(
                [self::equalTo($uuid1)],
                [self::equalTo($uuid2)]
            )
            ->willReturnOnConsecutiveCalls(
                self::throwException(new NotFoundException('rule', $uuid1->toString())),
                self::returnValue($rule)
            );

        self::assertSame(
            [
                '__version' => Descriptor::FORMAT_VERSION,
                'entities' => [
                    ['visited_value'],
                ],
            ],
            $this->serializer->serializeRules([$uuid1->toString(), $uuid2->toString()])
        );
    }
}
