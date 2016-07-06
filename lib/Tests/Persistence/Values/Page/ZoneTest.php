<?php

namespace Netgen\BlockManager\Tests\Persistence\Values;

use Netgen\BlockManager\Persistence\Values\Page\Layout;
use Netgen\BlockManager\Persistence\Values\Page\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        self::assertNull($zone->identifier);
        self::assertNull($zone->layoutId);
        self::assertNull($zone->status);
        self::assertNull($zone->linkedLayoutId);
        self::assertNull($zone->linkedZoneIdentifier);
    }

    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'identifier' => 'left',
                'layoutId' => 84,
                'status' => Layout::STATUS_PUBLISHED,
                'linkedLayoutId' => 24,
                'linkedZoneIdentifier' => 'top',
            )
        );

        self::assertEquals('left', $zone->identifier);
        self::assertEquals(84, $zone->layoutId);
        self::assertEquals(Layout::STATUS_PUBLISHED, $zone->status);
        self::assertEquals(24, $zone->linkedLayoutId);
        self::assertEquals('top', $zone->linkedZoneIdentifier);
    }
}
