<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\Core\Values\Page\Zone;

class ZoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     */
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        self::assertNull($zone->getId());
        self::assertNull($zone->getLayoutId());
        self::assertNull($zone->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
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
