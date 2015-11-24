<?php

namespace Netgen\BlockManager\Tests\API\Values;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;

class LayoutCreateStructTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        self::assertNull($layoutCreateStruct->identifier);
        self::assertNull($layoutCreateStruct->name);
        self::assertEquals(array(), $layoutCreateStruct->zoneIdentifiers);
    }

    public function testSetProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct(
            array(
                'identifier' => '3_zones_a',
                'name' => 'My layout',
                'zoneIdentifiers' => array('top', 'bottom'),
            )
        );

        self::assertEquals('3_zones_a', $layoutCreateStruct->identifier);
        self::assertEquals('My layout', $layoutCreateStruct->name);
        self::assertEquals(array('top', 'bottom'), $layoutCreateStruct->zoneIdentifiers);
    }
}
