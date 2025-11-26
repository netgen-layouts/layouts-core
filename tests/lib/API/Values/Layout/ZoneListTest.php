<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Zone;
use Netgen\Layouts\API\Values\Layout\ZoneList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ZoneList::class)]
final class ZoneListTest extends TestCase
{
    public function testGetZones(): void
    {
        $zones = ['one' => new Zone(), 'two' => new Zone()];

        self::assertSame($zones, ZoneList::fromArray($zones)->getZones());
    }

    public function testGetZoneIdentifiers(): void
    {
        $zones = [
            'left' => Zone::fromArray(['identifier' => 'left']),
            'right' => Zone::fromArray(['identifier' => 'right']),
        ];

        self::assertSame(['left', 'right'], ZoneList::fromArray($zones)->getZoneIdentifiers());
    }
}
