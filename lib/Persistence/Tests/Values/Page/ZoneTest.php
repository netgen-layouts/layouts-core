<?php

namespace Netgen\BlockManager\Persistence\Tests\Values;

use Netgen\BlockManager\Persistence\Values\Page\Zone;
use PHPUnit_Framework_TestCase;

class ZoneTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Page\Zone::__construct
     */
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        self::assertNull($zone->id);
        self::assertNull($zone->layoutId);
        self::assertNull($zone->identifier);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Page\Zone::__construct
     */
    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'id' => 42,
                'layoutId' => 84,
                'identifier' => 'top_left',
            )
        );

        self::assertEquals(42, $zone->id);
        self::assertEquals(84, $zone->layoutId);
        self::assertEquals('top_left', $zone->identifier);
    }
}
