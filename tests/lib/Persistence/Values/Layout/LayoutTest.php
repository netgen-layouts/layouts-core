<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Layout;

use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class LayoutTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $layout = new Layout();

        $this->assertNull($layout->id);
        $this->assertNull($layout->type);
        $this->assertNull($layout->name);
        $this->assertNull($layout->description);
        $this->assertNull($layout->shared);
        $this->assertNull($layout->created);
        $this->assertNull($layout->modified);
        $this->assertNull($layout->status);
    }

    public function testSetProperties()
    {
        $layout = new Layout(
            array(
                'id' => 42,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My description',
                'shared' => true,
                'created' => 123,
                'modified' => 456,
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $this->assertEquals(42, $layout->id);
        $this->assertEquals('4_zones_a', $layout->type);
        $this->assertEquals('My layout', $layout->name);
        $this->assertEquals('My description', $layout->description);
        $this->assertTrue($layout->shared);
        $this->assertEquals(123, $layout->created);
        $this->assertEquals(456, $layout->modified);
        $this->assertEquals(Value::STATUS_PUBLISHED, $layout->status);
    }
}
