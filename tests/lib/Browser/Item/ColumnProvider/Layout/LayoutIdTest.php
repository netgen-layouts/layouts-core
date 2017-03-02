<?php

namespace Netgen\BlockManager\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId;
use Netgen\BlockManager\Browser\Item\Layout\Item;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use PHPUnit\Framework\TestCase;

class LayoutIdTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new LayoutId();
    }

    /**
     * @covers \Netgen\BlockManager\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValue()
    {
        $item = new Item(
            new Layout(
                array(
                    'id' => 42,
                )
            )
        );

        $this->assertEquals(
            42,
            $this->provider->getValue($item)
        );
    }
}
