<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Browser\Item\ColumnProvider\Layout;

use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Created;
use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item;
use PHPUnit\Framework\TestCase;
use DateTime;

class CreatedTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Created
     */
    protected $provider;

    public function setUp()
    {
        $this->provider = new Created('d.m.Y H:i:s');
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Created::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\ColumnProvider\Layout\Created::getValue
     */
    public function testGetValue()
    {
        $date = new DateTime();
        $date->setDate(2016, 7, 17);
        $date->setTime(18, 15, 42);

        $item = new Item(
            new LayoutInfo(
                array(
                    'created' => $date,
                )
            )
        );

        $this->assertEquals(
            '17.07.2016 18:15:42',
            $this->provider->getValue($item)
        );
    }
}
