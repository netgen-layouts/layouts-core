<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Shared;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;

class SharedTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Shared
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new Shared();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Shared::getValue
     */
    public function testGetValue()
    {
        $item = new Item(
            new LayoutInfo(
                array(
                    'shared' => true,
                )
            )
        );

        $this->assertEquals(
            'Yes',
            $this->provider->getValue($item)
        );
    }
}
