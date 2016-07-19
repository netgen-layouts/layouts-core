<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
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
        $layoutTypeRegistry = new LayoutTypeRegistry();
        $layoutTypeRegistry->addLayoutType(new LayoutType('4_zones_a', true, '4 zones A', array()));

        $this->provider = new Type($layoutTypeRegistry);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Type::getValue
     */
    public function testGetValue()
    {
        $item = new Item(
            new LayoutInfo(
                array(
                    'type' => '4_zones_a',
                )
            )
        );

        self::assertEquals(
            '4 zones A',
            $this->provider->getValue($item)
        );
    }
}
