<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\Core\Values\Page\Zone;
use PHPUnit_Framework_TestCase;

class ZoneTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Zone::__construct
     * @covers \Netgen\BlockManager\API\Values\Zone::getId
     * @covers \Netgen\BlockManager\API\Values\Zone::getLayoutId
     * @covers \Netgen\BlockManager\API\Values\Zone::getIdentifier
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

        self::assertEquals(42, $zone->getId());
        self::assertEquals(84, $zone->getLayoutId());
        self::assertEquals('top_left', $zone->getIdentifier());
    }
}
