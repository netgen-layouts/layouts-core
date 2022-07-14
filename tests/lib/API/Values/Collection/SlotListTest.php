<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Collection;

use Netgen\Layouts\API\Values\Collection\Slot;
use Netgen\Layouts\API\Values\Collection\SlotList;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

final class SlotListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Collection\SlotList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', Slot::class),
                stdClass::class,
            ),
        );

        new SlotList([new Slot(), new stdClass(), new Slot()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\SlotList::__construct
     * @covers \Netgen\Layouts\API\Values\Collection\SlotList::getSlots
     */
    public function testGetSlots(): void
    {
        $slots = [new Slot(), new Slot()];

        self::assertSame($slots, (new SlotList($slots))->getSlots());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Collection\SlotList::getSlotIds
     */
    public function testGetSlotIds(): void
    {
        $uuid1 = Uuid::uuid4();
        $uuid2 = Uuid::uuid4();

        $slots = [Slot::fromArray(['id' => $uuid1]), Slot::fromArray(['id' => $uuid2])];

        self::assertSame([$uuid1, $uuid2], (new SlotList($slots))->getSlotIds());
    }
}
