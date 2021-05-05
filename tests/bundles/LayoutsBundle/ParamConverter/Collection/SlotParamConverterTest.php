<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ParamConverter\Collection;

use Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\SlotParamConverter;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class SlotParamConverterTest extends TestCase
{
    private MockObject $collectionServiceMock;

    private SlotParamConverter $paramConverter;

    protected function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->paramConverter = new SlotParamConverter($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\SlotParamConverter::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\SlotParamConverter::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['slotId'], $this->paramConverter->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\SlotParamConverter::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('slot', $this->paramConverter->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\SlotParamConverter::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Slot::class, $this->paramConverter->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\SlotParamConverter::loadValue
     */
    public function testLoadValue(): void
    {
        $slot = new Slot();

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadSlot')
            ->with(self::equalTo($uuid))
            ->willReturn($slot);

        self::assertSame(
            $slot,
            $this->paramConverter->loadValue(
                [
                    'slotId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ParamConverter\Collection\SlotParamConverter::loadValue
     */
    public function testLoadValueDraft(): void
    {
        $slot = new Slot();

        $uuid = Uuid::uuid4();

        $this->collectionServiceMock
            ->expects(self::once())
            ->method('loadSlotDraft')
            ->with(self::equalTo($uuid))
            ->willReturn($slot);

        self::assertSame(
            $slot,
            $this->paramConverter->loadValue(
                [
                    'slotId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
