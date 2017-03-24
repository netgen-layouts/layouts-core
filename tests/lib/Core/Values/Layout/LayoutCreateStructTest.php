<?php

namespace Netgen\BlockManager\Tests\Core\Values\Layout;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;

class LayoutCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        $this->assertNull($layoutCreateStruct->layoutType);
        $this->assertNull($layoutCreateStruct->name);
        $this->assertNull($layoutCreateStruct->shared);
    }

    public function testSetProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct(
            array(
                'layoutType' => new LayoutType(),
                'name' => 'My layout',
                'shared' => true,
            )
        );

        $this->assertEquals(new LayoutType(), $layoutCreateStruct->layoutType);
        $this->assertEquals('My layout', $layoutCreateStruct->name);
        $this->assertTrue($layoutCreateStruct->shared);
    }
}
