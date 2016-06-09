<?php

namespace Netgen\BlockManager\Tests\Persistence\Values;

use Netgen\BlockManager\Persistence\Values\Page\Layout;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $layout = new Layout();

        self::assertNull($layout->id);
        self::assertNull($layout->type);
        self::assertNull($layout->name);
        self::assertNull($layout->created);
        self::assertNull($layout->modified);
    }

    public function testSetProperties()
    {
        $layout = new Layout(
            array(
                'id' => 42,
                'type' => '3_zones_a',
                'name' => 'My layout',
                'created' => 123,
                'modified' => 456,
            )
        );

        self::assertEquals(42, $layout->id);
        self::assertEquals('3_zones_a', $layout->type);
        self::assertEquals('My layout', $layout->name);
        self::assertEquals(123, $layout->created);
        self::assertEquals(456, $layout->modified);
    }
}
