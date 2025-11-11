<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(SlotValueResolver::class)]
final class SlotValueResolverTest extends TestCase
{
    private MockObject $collectionServiceMock;

    private SlotValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceMock = $this->createMock(CollectionService::class);

        $this->valueResolver = new SlotValueResolver($this->collectionServiceMock);
    }

    public function testGetSourceAttributeName(): void
    {
        self::assertSame(['slotId'], $this->valueResolver->getSourceAttributeNames());
    }

    public function testGetDestinationAttributeName(): void
    {
        self::assertSame('slot', $this->valueResolver->getDestinationAttributeName());
    }

    public function testGetSupportedClass(): void
    {
        self::assertSame(Slot::class, $this->valueResolver->getSupportedClass());
    }

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
