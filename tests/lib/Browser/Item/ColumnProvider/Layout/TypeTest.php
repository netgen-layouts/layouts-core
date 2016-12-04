<?php

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Core\Values\Page\Layout;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\Type
     */
    protected $provider;

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
                array(
                    'layoutType' => new LayoutType(
                        array(
                            'name' => '4 zones A',
                        )
                    ),
                )
            )
        );

        $this->assertEquals(
            '4 zones A',
            $this->provider->getValue($item)
        );
    }
}
