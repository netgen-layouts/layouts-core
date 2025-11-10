<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class SlotValueResolverTest extends TestCase
{
    private MockObject $collectionServiceMock;

    private SlotValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->valueResolver = new SlotValueResolver($this->collectionServiceMock);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver::getSourceAttributeNames
     */
    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['slotId'], $this->valueResolver->getSourceAttributeNames());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver::getDestinationAttributeName
     */
    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('slot', $this->valueResolver->getDestinationAttributeName());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver::getSupportedClass
     */
    public function testGetSupportedClass(): void
    {
        self::assertSame(Slot::class, $this->valueResolver->getSupportedClass());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver::loadValue
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
            $this->valueResolver->loadValue(
                [
                    'slotId' => $uuid->toString(),
                    'status' => 'published',
                ],
            ),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver::loadValue
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
            $this->valueResolver->loadValue(
                [
                    'slotId' => $uuid->toString(),
                    'status' => 'draft',
                ],
            ),
        );
    }
}
