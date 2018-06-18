<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\Layout;

use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class ZoneTest extends TestCase
{
    public function testSetProperties(): void
    {
        $zone = new Zone(
            [
                'identifier' => 'left',
                'layoutId' => 84,
                'status' => Value::STATUS_PUBLISHED,
                'rootBlockId' => 42,
                'linkedLayoutId' => 24,
                'linkedZoneIdentifier' => 'top',
            ]
        );

        $this->assertSame('left', $zone->identifier);
        $this->assertSame(84, $zone->layoutId);
        $this->assertSame(Value::STATUS_PUBLISHED, $zone->status);
        $this->assertSame(42, $zone->rootBlockId);
        $this->assertSame(24, $zone->linkedLayoutId);
        $this->assertSame('top', $zone->linkedZoneIdentifier);
    }
}
