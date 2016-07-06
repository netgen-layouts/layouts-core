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
        self::assertNull($layout->shared);
        self::assertNull($layout->created);
        self::assertNull($layout->modified);
        self::assertNull($layout->status);
    }

    public function testSetProperties()
    {
        $layout = new Layout(
            array(
                'id' => 42,
                'type' => '4_zones_a',
                'name' => 'My layout',
                'shared' => true,
                'created' => 123,
                'modified' => 456,
                'status' => Layout::STATUS_PUBLISHED,
            )
        );

        self::assertEquals(42, $layout->id);
        self::assertEquals('4_zones_a', $layout->type);
        self::assertEquals('My layout', $layout->name);
        self::assertEquals(true, $layout->shared);
        self::assertEquals(123, $layout->created);
        self::assertEquals(456, $layout->modified);
        self::assertEquals(Layout::STATUS_PUBLISHED, $layout->status);
    }
}
