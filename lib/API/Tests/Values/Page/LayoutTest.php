<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\Core\Values\Page\Layout;
use PHPUnit_Framework_TestCase;
use DateTime;

class LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Layout::__construct
     * @covers \Netgen\BlockManager\API\Values\Layout::getId
     * @covers \Netgen\BlockManager\API\Values\Layout::getParentId
     * @covers \Netgen\BlockManager\API\Values\Layout::getIdentifier
     * @covers \Netgen\BlockManager\API\Values\Layout::getCreated
     * @covers \Netgen\BlockManager\API\Values\Layout::getModified
     * @covers \Netgen\BlockManager\API\Values\Layout::getZones
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
                'identifier' => '3_zones_a',
                'created' => $createdDate,
                'modified' => $modifiedDate,
                'zones' => array('top_left', 'top_right')
            )
        );

        self::assertEquals(42, $layout->getId());
        self::assertEquals(84, $layout->getParentId());
        self::assertEquals('3_zones_a', $layout->getIdentifier());
        self::assertEquals($createdDate, $layout->getCreated());
        self::assertEquals($modifiedDate, $layout->getModified());
        self::assertEquals(array('top_left', 'top_right'), $layout->getZones());
    }
}
