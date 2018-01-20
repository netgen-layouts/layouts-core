<?php

namespace Netgen\BlockManager\Tests\Core\Values\Layout;

use Netgen\BlockManager\API\Values\Layout\LayoutCreateStruct;
use Netgen\BlockManager\Layout\Type\LayoutType;
use PHPUnit\Framework\TestCase;

final class LayoutCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        $this->assertNull($layoutCreateStruct->layoutType);
        $this->assertNull($layoutCreateStruct->name);
        $this->assertNull($layoutCreateStruct->description);
        $this->assertFalse($layoutCreateStruct->shared);
        $this->assertNull($layoutCreateStruct->mainLocale);
    }

    public function testSetProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct(
            array(
                'layoutType' => new LayoutType(),
                'name' => 'My layout',
                'description' => 'My description',
                'shared' => true,
                'mainLocale' => 'en',
            )
        );

        $this->assertEquals(new LayoutType(), $layoutCreateStruct->layoutType);
        $this->assertEquals('My layout', $layoutCreateStruct->name);
        $this->assertEquals('My description', $layoutCreateStruct->description);
        $this->assertTrue($layoutCreateStruct->shared);
        $this->assertEquals('en', $layoutCreateStruct->mainLocale);
    }
}
