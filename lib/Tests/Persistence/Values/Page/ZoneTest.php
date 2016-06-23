<?php

namespace Netgen\BlockManager\Tests\Persistence\Values;

use Netgen\BlockManager\Persistence\Values\Page\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        self::assertNull($zone->identifier);
        self::assertNull($zone->layoutId);
    }

    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'identifier' => 'left',
                'layoutId' => 84,
            )
        );

        self::assertEquals('left', $zone->identifier);
        self::assertEquals(84, $zone->layoutId);
    }
}
