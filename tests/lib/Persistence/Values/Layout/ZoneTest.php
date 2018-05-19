<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Layout;

use Netgen\BlockManager\Persistence\Values\Layout\Zone;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class ZoneTest extends TestCase
{
    public function testSetProperties()
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

        $this->assertEquals('left', $zone->identifier);
        $this->assertEquals(84, $zone->layoutId);
        $this->assertEquals(Value::STATUS_PUBLISHED, $zone->status);
        $this->assertEquals(42, $zone->rootBlockId);
        $this->assertEquals(24, $zone->linkedLayoutId);
        $this->assertEquals('top', $zone->linkedZoneIdentifier);
    }
}
