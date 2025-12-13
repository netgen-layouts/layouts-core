<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Collection\SlotList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(SlotList::class)]
final class SlotListTest extends TestCase
{
    public function testGetSlots(): void
    {
        $slots = [new Slot(), new Slot()];

        self::assertSame($slots, SlotList::fromArray($slots)->getSlots());
    }

    public function testGetSlotIds(): void
    {
        $uuid1 = Uuid::v4();
        $uuid2 = Uuid::v4();

        $slots = [Slot::fromArray(['id' => $uuid1]), Slot::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], SlotList::fromArray($slots)->getSlotIds());
    }
}
