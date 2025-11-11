<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Layout;

use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Status;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class ZoneTest extends TestCase
{
    public function testSetProperties(): void
    {
        $zone = Zone::fromArray(
            [
                'identifier' => 'left',
                'layoutId' => 84,
                'layoutUuid' => 'f06f245a-f951-52c8-bfa3-84c80154eadc',
                'status' => Status::Published,
                'rootBlockId' => 42,
                'linkedLayoutUuid' => 'd8e55af7-cf62-5f28-ae15-331b457d82e9',
                'linkedZoneIdentifier' => 'top',
            ],
        );

        self::assertSame('left', $zone->identifier);
        self::assertSame(84, $zone->layoutId);
        self::assertSame('f06f245a-f951-52c8-bfa3-84c80154eadc', $zone->layoutUuid);
        self::assertSame(Status::Published, $zone->status);
        self::assertSame(42, $zone->rootBlockId);
        self::assertSame('d8e55af7-cf62-5f28-ae15-331b457d82e9', $zone->linkedLayoutUuid);
        self::assertSame('top', $zone->linkedZoneIdentifier);
    }
}
