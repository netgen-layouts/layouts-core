<?php

namespace Netgen\BlockManager\API\Tests\Values;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use PHPUnit_Framework_TestCase;

class LayoutCreateStructTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\BlockCreateStruct::__construct
     */
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
