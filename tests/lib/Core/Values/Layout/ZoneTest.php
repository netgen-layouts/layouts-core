<?php

namespace Netgen\BlockManager\Tests\Core\Values\Layout;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Layout\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getLinkedZone
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::isPublished
     */
    public function testSetDefaultProperties()
    {
        $zone = new Zone();

        $this->assertNull($zone->getIdentifier());
        $this->assertNull($zone->getLayoutId());
        $this->assertNull($zone->getStatus());
        $this->assertNull($zone->getLinkedZone());
        $this->assertNull($zone->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::getLinkedZone
     * @covers \Netgen\BlockManager\Core\Values\Layout\Zone::isPublished
     */
    public function testSetProperties()
    {
        $zone = new Zone(
            array(
                'identifier' => 'left',
                'layoutId' => 84,
                'status' => Value::STATUS_PUBLISHED,
                'linkedZone' => null,
                'published' => true,
            )
        );

        $this->assertEquals('left', $zone->getIdentifier());
        $this->assertEquals(84, $zone->getLayoutId());
        $this->assertTrue($zone->isPublished());
        $this->assertNull($zone->getLinkedZone());
        $this->assertTrue($zone->isPublished());
    }
}
