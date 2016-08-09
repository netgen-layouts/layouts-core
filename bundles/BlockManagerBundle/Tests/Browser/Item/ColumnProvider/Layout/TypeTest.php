<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use Netgen\BlockManager\Tests\Configuration\Stubs\LayoutType;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Type;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Type
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new Type();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Type::getValue
     */
    public function testGetValue()
    {
        $item = new Item(
            new LayoutInfo(
                array(
                    'layoutType' => new LayoutType('4_zones_a', array(), '4 zones A'),
                )
            )
        );

        $this->assertEquals(
            '4 zones A',
            $this->provider->getValue($item)
        );
    }
}
