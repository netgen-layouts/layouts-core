<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\LayoutId;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;

class LayoutIdTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\LayoutId
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new LayoutId();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\LayoutId::getValue
     */
    public function testGetValue()
    {
        $item = new Item(
            new LayoutInfo(
                array(
                    'id' => 42,
                )
            )
        );

        self::assertEquals(
            42,
            $this->provider->getValue($item)
        );
    }
}
