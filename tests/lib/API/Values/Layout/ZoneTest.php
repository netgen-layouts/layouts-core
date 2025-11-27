<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Layout;

use Netgen\Layouts\API\Values\Layout\Zone;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

#[CoversClass(Zone::class)]
final class ZoneTest extends TestCase
{
    public function testSetProperties(): void
    {
        $linkedZone = new Zone();

        $layoutUuid = Uuid::uuid4();

        $zone = Zone::fromArray(
            [
                'identifier' => 'left',
                'layoutId' => $layoutUuid,
                'linkedZone' => $linkedZone,
            ],
        );

        self::assertSame('left', $zone->identifier);
        self::assertSame($layoutUuid, $zone->layoutId);
        self::assertTrue($zone->hasLinkedZone);
        self::assertSame($linkedZone, $zone->linkedZone);
    }
}
