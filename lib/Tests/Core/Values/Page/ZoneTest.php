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
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedZoneIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::isEmpty
     */
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        self::assertNull($zone->getIdentifier());
        self::assertNull($zone->getLayoutId());
        self::assertNull($zone->getStatus());
        self::assertNull($zone->getLinkedLayoutId());
        self::assertNull($zone->getLinkedZoneIdentifier());
        self::assertEquals(array(), $zone->getBlocks());
        self::assertTrue($zone->isEmpty());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedZoneIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::isEmpty
     */
    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'identifier' => 'left',
                'layoutId' => 84,
                'status' => Layout::STATUS_PUBLISHED,
                'linkedLayoutId' => null,
                'linkedZoneIdentifier' => null,
                'blocks' => array(
                    new Block(),
                ),
            )
        );

        self::assertEquals('left', $zone->getIdentifier());
        self::assertEquals(84, $zone->getLayoutId());
        self::assertEquals(Layout::STATUS_PUBLISHED, $zone->getStatus());
        self::assertEquals(array(new Block()), $zone->getBlocks());
        self::assertNull($zone->getLinkedLayoutId());
        self::assertNull($zone->getLinkedZoneIdentifier());
        self::assertFalse($zone->isEmpty());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedZoneIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getBlocks
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::isEmpty
     */
    public function testIsEmptyWithLinkedLayout()
    {
        $zone = new Zone(
            array(
                'identifier' => 'left',
                'layoutId' => 84,
                'status' => Layout::STATUS_PUBLISHED,
                'linkedLayoutId' => 42,
                'linkedZoneIdentifier' => 'top',
                'blocks' => array(),
            )
        );

        self::assertEquals('left', $zone->getIdentifier());
        self::assertEquals(84, $zone->getLayoutId());
        self::assertEquals(Layout::STATUS_PUBLISHED, $zone->getStatus());
        self::assertEquals(array(), $zone->getBlocks());
        self::assertEquals(42, $zone->getLinkedLayoutId());
        self::assertEquals('top', $zone->getLinkedZoneIdentifier());
        self::assertFalse($zone->isEmpty());
    }
}
