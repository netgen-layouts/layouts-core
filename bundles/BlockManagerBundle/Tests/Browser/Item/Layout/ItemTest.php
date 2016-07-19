<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Browser\Item\Layout;

use Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item;
use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\LayoutInfo
     */
    protected $layout;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item
     */
    protected $item;

    public function setUp()
    {
        $this->layout = new LayoutInfo(array('id' => 42, 'name' => 'My layout'));

        $this->item = new Item($this->layout);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item::getType
     */
    public function testGetType()
    {
        self::assertEquals('ngbm_layout', $this->item->getType());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item::getValue
     */
    public function testGetValue()
    {
        self::assertEquals(42, $this->item->getValue());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item::getName
     */
    public function testGetName()
    {
        self::assertEquals('My layout', $this->item->getName());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item::getParentId
     */
    public function testGetParentId()
    {
        self::assertEquals(0, $this->item->getParentId());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item::isVisible
     */
    public function testIsVisible()
    {
        self::assertTrue($this->item->isVisible());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Browser\Item\Layout\Item::getLayout
     */
    public function testGetLayout()
    {
        self::assertEquals($this->layout, $this->item->getLayout());
    }
}
