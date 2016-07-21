<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\LayoutCreateStruct;
use PHPUnit\Framework\TestCase;

class LayoutCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct();

        $this->assertNull($layoutCreateStruct->type);
        $this->assertNull($layoutCreateStruct->name);
        $this->assertNull($layoutCreateStruct->shared);
    }

    public function testSetProperties()
    {
        $layoutCreateStruct = new LayoutCreateStruct(
            array(
                'type' => '4_zones_a',
                'name' => 'My layout',
                'shared' => true,
            )
        );

        $this->assertEquals('4_zones_a', $layoutCreateStruct->type);
        $this->assertEquals('My layout', $layoutCreateStruct->name);
        $this->assertTrue($layoutCreateStruct->shared);
    }
}
