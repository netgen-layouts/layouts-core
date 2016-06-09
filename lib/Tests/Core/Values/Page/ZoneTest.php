<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Core\Values\Page\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     */
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        self::assertNull($zone->getIdentifier());
        self::assertNull($zone->getLayoutId());
        self::assertNull($zone->getStatus());
        self::assertEquals(array(), $zone->getBlocks());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     */
    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'identifier' => 'top_left',
                'layoutId' => 84,
                'status' => Layout::STATUS_PUBLISHED,
                'blocks' => array(
                    new Block(),
                ),
            )
        );

        self::assertEquals('top_left', $zone->getIdentifier());
        self::assertEquals(84, $zone->getLayoutId());
        self::assertEquals(Layout::STATUS_PUBLISHED, $zone->getStatus());
        self::assertEquals(array(new Block()), $zone->getBlocks());
    }
}
