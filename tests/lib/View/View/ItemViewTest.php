<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\View\View\ItemView;
use PHPUnit\Framework\TestCase;

final class ItemViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\ItemInterface
     */
    private $item;

    /**
     * @var \Netgen\BlockManager\View\View\ItemViewInterface
     */
    private $view;

    public function setUp()
    {
        $this->item = new Item(
            array(
                'value' => 42,
                'valueType' => 'type',
            )
        );

        $this->view = new ItemView(
            array(
                'item' => $this->item,
                'view_type' => 'view_type',
            )
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('item', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::__construct
     * @covers \Netgen\BlockManager\View\View\ItemView::getItem
     */
    public function testGetItem()
    {
        $this->assertEquals($this->item, $this->view->getItem());
        $this->assertEquals(
            array(
                'param' => 'value',
                'item' => $this->item,
                'view_type' => 'view_type',
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::getViewType
     */
    public function testGetViewType()
    {
        $this->assertEquals('view_type', $this->view->getViewType());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('item_view', $this->view->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::jsonSerialize
     */
    public function testJsonSerialize()
    {
        $this->assertEquals(
            array(
                'value' => 42,
                'valueType' => 'type',
                'viewType' => 'view_type',
            ),
            $this->view->jsonSerialize()
        );
    }
}
