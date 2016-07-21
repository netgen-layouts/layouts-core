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

        $this->assertNull($zone->getIdentifier());
        $this->assertNull($zone->getLayoutId());
        $this->assertNull($zone->getStatus());
        $this->assertNull($zone->getLinkedLayoutId());
        $this->assertNull($zone->getLinkedZoneIdentifier());
        $this->assertEquals(array(), $zone->getBlocks());
        $this->assertTrue($zone->isEmpty());
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

        $this->assertEquals('left', $zone->getIdentifier());
        $this->assertEquals(84, $zone->getLayoutId());
        $this->assertEquals(Layout::STATUS_PUBLISHED, $zone->getStatus());
        $this->assertEquals(array(new Block()), $zone->getBlocks());
        $this->assertNull($zone->getLinkedLayoutId());
        $this->assertNull($zone->getLinkedZoneIdentifier());
        $this->assertFalse($zone->isEmpty());
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

        $this->assertEquals('left', $zone->getIdentifier());
        $this->assertEquals(84, $zone->getLayoutId());
        $this->assertEquals(Layout::STATUS_PUBLISHED, $zone->getStatus());
        $this->assertEquals(array(), $zone->getBlocks());
        $this->assertEquals(42, $zone->getLinkedLayoutId());
        $this->assertEquals('top', $zone->getLinkedZoneIdentifier());
        $this->assertFalse($zone->isEmpty());
    }
}
