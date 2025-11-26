<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Slot;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Slot::class)]
final class SlotTest extends TestCase
{
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

        self::assertSame($slotUuid->toString(), $slot->id->toString());
        self::assertSame($collectionUuid->toString(), $slot->collectionId->toString());
        self::assertSame(3, $slot->position);
        self::assertSame('overlay', $slot->viewType);
        self::assertFalse($slot->isEmpty);
    }
}
