<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use Netgen\BlockManager\API\Values\Page\Layout;
use PHPUnit\Framework\TestCase;

class LayoutCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        self::assertNull($layoutCreateStruct->type);
        self::assertNull($layoutCreateStruct->name);
    }

    public function testSetProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct(
            array(
                'type' => '3_zones_a',
                'name' => 'My layout',
            )
        );

        self::assertEquals('3_zones_a', $layoutCreateStruct->type);
        self::assertEquals('My layout', $layoutCreateStruct->name);
    }
}
