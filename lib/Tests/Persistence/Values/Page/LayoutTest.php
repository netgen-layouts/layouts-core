<?php

namespace Netgen\BlockManager\Tests\Persistence\Values;

use Netgen\BlockManager\Persistence\Values\Page\Layout;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Page\Layout::__construct
     */
    public function testSetDefaultProperties()
    {
        $layout = new Layout();

        self::assertNull($layout->id);
        self::assertNull($layout->parentId);
        self::assertNull($layout->identifier);
        self::assertNull($layout->name);
        self::assertNull($layout->created);
        self::assertNull($layout->modified);
    }

    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Page\Layout::__construct
     */
    public function testSetProperties()
    {
        $layout = new Layout(
            array(
                'id' => 42,
                'parentId' => 84,
                'identifier' => '3_zones_a',
                'name' => 'My layout',
                'created' => 123,
                'modified' => 456,
            )
        );

        self::assertEquals(42, $layout->id);
        self::assertEquals(84, $layout->parentId);
        self::assertEquals('3_zones_a', $layout->identifier);
        self::assertEquals('My layout', $layout->name);
        self::assertEquals(123, $layout->created);
        self::assertEquals(456, $layout->modified);
    }
}
