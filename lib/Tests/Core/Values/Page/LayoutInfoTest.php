<?php

namespace Netgen\BlockManager\Tests\Core\Values\Page;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use PHPUnit\Framework\TestCase;
use DateTime;

class LayoutInfoTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getType
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getCreated
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getModified
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::isShared
     */
    public function testSetDefaultProperties()
    {
        $layout = new LayoutInfo();

        self::assertNull($layout->getId());
        self::assertNull($layout->getType());
        self::assertNull($layout->getName());
        self::assertNull($layout->getCreated());
        self::assertNull($layout->getModified());
        self::assertNull($layout->getStatus());
        self::assertNull($layout->isShared());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::__construct
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getId
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getType
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getName
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getCreated
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getModified
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::getStatus
     * @covers \Netgen\BlockManager\Core\Values\Page\Layout::isShared
     */
    public function testSetProperties()
    {
        $createdDate = new DateTime();
        $createdDate->setTimestamp(123);

        $modifiedDate = new DateTime();
        $modifiedDate->setTimestamp(456);

        $layout = new LayoutInfo(
            array(
                'id' => 42,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'created' => $createdDate,
                'modified' => $modifiedDate,
                'status' => Layout::STATUS_PUBLISHED,
                'shared' => true,
            )
        );

        self::assertEquals(42, $layout->getId());
        self::assertEquals('4_zones_a', $layout->getType());
        self::assertEquals('My layout', $layout->getName());
        self::assertEquals($createdDate, $layout->getCreated());
        self::assertEquals($modifiedDate, $layout->getModified());
        self::assertEquals(Layout::STATUS_PUBLISHED, $layout->getStatus());
        self::assertTrue($layout->isShared());
    }
}
