<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;

class LayoutCreateStructTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        self::assertNull($layoutCreateStruct->layoutIdentifier);
        self::assertEquals(array(), $layoutCreateStruct->zoneIdentifiers);
    }

    public function testSetProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct(
            array(
                'layoutIdentifier' => '3_zones_a',
                'zoneIdentifiers' => array('top', 'bottom'),
            )
        );

        self::assertEquals('3_zones_a', $layoutCreateStruct->layoutIdentifier);
        self::assertEquals(array('top', 'bottom'), $layoutCreateStruct->zoneIdentifiers);
    }
}
