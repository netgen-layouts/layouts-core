<?php

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;

class LayoutCreateStructTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        self::assertNull($layoutCreateStruct->type);
        self::assertNull($layoutCreateStruct->name);
        self::assertEquals(Layout::STATUS_DRAFT, $layoutCreateStruct->status);
        self::assertEquals(array(), $layoutCreateStruct->zoneIdentifiers);
    }

    public function testSetProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct(
            array(
                'type' => '3_zones_a',
                'name' => 'My layout',
                'status' => Layout::STATUS_PUBLISHED,
                'zoneIdentifiers' => array('top', 'bottom'),
            )
        );

        self::assertEquals('3_zones_a', $layoutCreateStruct->type);
        self::assertEquals('My layout', $layoutCreateStruct->name);
        self::assertEquals(Layout::STATUS_PUBLISHED, $layoutCreateStruct->status);
        self::assertEquals(array('top', 'bottom'), $layoutCreateStruct->zoneIdentifiers);
    }
}
