<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\Page\Zone;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Configuration\Stubs\LayoutType;
use PHPUnit\Framework\TestCase;
use DateTime;

class LayoutTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getLayoutType
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getCreated
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getModified
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::isShared
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getZones
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getZone
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::hasZone
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::isPublished
     */
    public function testSetDefaultProperties()
    {
        $layout = new Layout();

        $this->assertNull($layout->getId());
        $this->assertNull($layout->getLayoutType());
        $this->assertNull($layout->getName());
        $this->assertNull($layout->getCreated());
        $this->assertNull($layout->getModified());
        $this->assertNull($layout->getStatus());
        $this->assertNull($layout->isShared());
        $this->assertEquals(array(), $layout->getZones());
        $this->assertNull($layout->getZone('test'));
        $this->assertFalse($layout->hasZone('test'));
        $this->assertNull($layout->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getLayoutType
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getCreated
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getModified
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::isShared
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getZones
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getZone
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::hasZone
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::isPublished
     */
    public function testSetProperties()
    {
        $createdDate = new DateTime();
        $createdDate->setTimestamp(123);

        $modifiedDate = new DateTime();
        $modifiedDate->setTimestamp(456);

        $layout = new Layout(
            array(
                'id' => 42,
                'layoutType' => new LayoutType('4_zones_a'),
                'name' => 'My layout',
                'created' => $createdDate,
                'modified' => $modifiedDate,
                'status' => Value::STATUS_PUBLISHED,
                'shared' => true,
                'zones' => array('left' => new Zone(), 'right' => new Zone()),
                'published' => true,
            )
        );

        $this->assertEquals(42, $layout->getId());
        $this->assertEquals(new LayoutType('4_zones_a'), $layout->getLayoutType());
        $this->assertEquals('My layout', $layout->getName());
        $this->assertEquals($createdDate, $layout->getCreated());
        $this->assertEquals($modifiedDate, $layout->getModified());
        $this->assertTrue($layout->isPublished());
        $this->assertTrue($layout->isShared());
        $this->assertEquals(
            array('left' => new Zone(), 'right' => new Zone()),
            $layout->getZones()
        );
        $this->assertNull($layout->getZone('test'));
        $this->assertFalse($layout->hasZone('test'));
        $this->assertInstanceOf(Zone::class, $layout->getZone('left'));
        $this->assertTrue($layout->hasZone('left'));
        $this->assertTrue($layout->isPublished());
    }
}
