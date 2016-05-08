<?php

namespace Netgen\BlockManager\Tests\API\Values\Page;

use Netgen\BlockManager\Core\Values\Page\Layout;
use DateTime;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getParentId
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getType
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getCreated
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getModified
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getZones
     */
    public function testSetDefaultProperties()
    {
        $layout = new Layout();

        self::assertNull($layout->getId());
        self::assertNull($layout->getParentId());
        self::assertNull($layout->getType());
        self::assertNull($layout->getName());
        self::assertNull($layout->getCreated());
        self::assertNull($layout->getModified());
        self::assertNull($layout->getStatus());
        self::assertEquals(array(), $layout->getZones());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getParentId
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getType
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getCreated
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getModified
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getZones
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
                'parentId' => 84,
                'type' => '3_zones_a',
                'name' => 'My layout',
                'created' => $createdDate,
                'modified' => $modifiedDate,
                'status' => Layout::STATUS_PUBLISHED,
                'zones' => array('top_left', 'top_right'),
            )
        );

        self::assertEquals(42, $layout->getId());
        self::assertEquals(84, $layout->getParentId());
        self::assertEquals('3_zones_a', $layout->getType());
        self::assertEquals('My layout', $layout->getName());
        self::assertEquals($createdDate, $layout->getCreated());
        self::assertEquals($modifiedDate, $layout->getModified());
        self::assertEquals(Layout::STATUS_PUBLISHED, $layout->getStatus());
        self::assertEquals(array('top_left', 'top_right'), $layout->getZones());
    }
}
