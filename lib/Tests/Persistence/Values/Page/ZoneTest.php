<?php

namespace Netgen\BlockManager\Tests\Persistence\Values;

use Netgen\BlockManager\Persistence\Values\Page\Zone;

class ZoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Page\Zone::__construct
     */
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        self::assertNull($zone->identifier);
        self::assertNull($zone->layoutId);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Page\Zone::__construct
     */
    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'identifier' => 'top_left',
                'layoutId' => 84,
            )
        );

        self::assertEquals('top_left', $zone->identifier);
        self::assertEquals(84, $zone->layoutId);
    }
}
