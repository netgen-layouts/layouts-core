<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Page\Zone;
use PHPUnit\Framework\TestCase;

class ZoneTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedZone
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::isPublished
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
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLayoutId
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::getLinkedZone
     * @covers \Netgen\BlockManager\Core\Values\Page\Zone::isPublished
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
