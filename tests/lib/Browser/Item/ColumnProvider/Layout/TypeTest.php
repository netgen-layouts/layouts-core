<?php

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\ContentBrowser\Tests\Stubs\Item as StubItem;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type
     */
    private $provider;

    public function setUp()
    {
        $this->provider = new Type();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type::getValue
     */
    public function testGetValue()
    {
        $item = new Item(
            new Layout(
                [
                    'layoutType' => new LayoutType(
                        [
                            'name' => '4 zones A',
                        ]
                    ),
                ]
            )
        );

        $this->assertEquals(
            '4 zones A',
            $this->provider->getValue($item)
        );
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type::getValue
     */
    public function testGetValueWithInvalidItem()
    {
        $this->assertNull($this->provider->getValue(new StubItem()));
    }
}
