<?php

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;

class ZoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     */
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        self::assertNull($zone->getId());
        self::assertNull($zone->getLayoutId());
        self::assertNull($zone->getIdentifier());
        self::assertNull($zone->getStatus());
        self::assertEquals(array(), $zone->getBlocks());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     */
    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'id' => 42,
                'layoutId' => 84,
                'identifier' => 'top_left',
                'status' => Layout::STATUS_PUBLISHED,
                'blocks' => array(
                    new Block(),
                ),
            )
        );

        self::assertEquals(42, $zone->getId());
        self::assertEquals(84, $zone->getLayoutId());
        self::assertEquals('top_left', $zone->getIdentifier());
        self::assertEquals(Layout::STATUS_PUBLISHED, $zone->getStatus());
        self::assertEquals(array(new Block()), $zone->getBlocks());
    }
}
