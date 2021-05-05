<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Slot;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class SlotTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Collection\Slot::getCollectionId
     * @covers \Netgen\Layouts\API\Values\Collection\Slot::getId
     * @covers \Netgen\Layouts\API\Values\Collection\Slot::getPosition
     * @covers \Netgen\Layouts\API\Values\Collection\Slot::getViewType
     * @covers \Netgen\Layouts\API\Values\Collection\Slot::isEmpty
     */
    public function testSetProperties(): void
    {
        $slotUuid = Uuid::uuid4();
        $collectionUuid = Uuid::uuid4();

        $slot = Slot::fromArray(
            [
                'id' => $slotUuid,
                'collectionId' => $collectionUuid,
                'position' => 3,
                'viewType' => 'overlay',
            ],
        );

        self::assertSame($slotUuid->toString(), $slot->getId()->toString());
        self::assertSame($collectionUuid->toString(), $slot->getCollectionId()->toString());
        self::assertSame(3, $slot->getPosition());
        self::assertSame('overlay', $slot->getViewType());
        self::assertFalse($slot->isEmpty());
    }
}
