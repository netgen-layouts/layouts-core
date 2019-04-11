<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\Layout;

use Netgen\Layouts\Persistence\Values\Layout\Zone;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class ZoneTest extends TestCase
{
    public function testSetProperties(): void
    {
        $zone = Zone::fromArray(
            [
                'identifier' => 'left',
                'layoutId' => 84,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 42,
                'linkedLayoutId' => 24,
                'linkedZoneIdentifier' => 'top',
            ]
        );

        self::assertSame('left', $zone->identifier);
        self::assertSame(84, $zone->layoutId);
        self::assertSame(Value::STATUS_PUBLISHED, $zone->status);
        self::assertSame(42, $zone->rootBlockId);
        self::assertSame(24, $zone->linkedLayoutId);
        self::assertSame('top', $zone->linkedZoneIdentifier);
    }
}
