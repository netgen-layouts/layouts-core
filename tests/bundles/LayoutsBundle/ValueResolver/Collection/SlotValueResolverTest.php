<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\ValueResolver\Collection;

use Netgen\Bundle\LayoutsBundle\ValueResolver\Collection\SlotValueResolver;
use Netgen\Layouts\API\Service\CollectionService;
use Netgen\Layouts\API\Values\Collection\Slot;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(SlotValueResolver::class)]
final class SlotValueResolverTest extends TestCase
{
    private Stub&CollectionService $collectionServiceStub;

    private SlotValueResolver $valueResolver;

    protected function setUp(): void
    {
        $this->collectionServiceStub = self::createStub(CollectionService::class);

        $this->valueResolver = new SlotValueResolver($this->collectionServiceStub);
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

        $uuid = Uuid::v4();

        $this->collectionServiceStub
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

        $uuid = Uuid::v4();

        $this->collectionServiceStub
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
