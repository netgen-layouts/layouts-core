<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Collection;

use Netgen\Layouts\Persistence\Values\Collection\Slot;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class SlotTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $slot = Slot::fromArray(
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'collectionId' => 30,
                'collectionUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'position' => 3,
                'viewType' => 'my_view_type',
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        self::assertSame(42, $slot->id);
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $slot->uuid);
        self::assertSame(30, $slot->collectionId);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $slot->collectionUuid);
        self::assertSame(3, $slot->position);
        self::assertSame('my_view_type', $slot->viewType);
        self::assertSame(Value::STATUS_PUBLISHED, $slot->status);
    }
}
