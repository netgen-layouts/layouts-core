<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\Layout;

use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class LayoutTest extends TestCase
{
    public function testSetProperties(): void
    {
        $layout = new Layout(
            [
                'id' => 42,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'description' => 'My description',
                'shared' => true,
                'created' => 123,
                'modified' => 456,
                'status' => Value::STATUS_PUBLISHED,
            ]
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
