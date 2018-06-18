<?php

declare(strict_types=1);

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

    public function setUp(): void
    {
        $this->item = new Item(
            [
                'value' => 42,
                'valueType' => 'type',
            ]
        );

        $this->view = new ItemView(
            [
                'item' => $this->item,
                'view_type' => 'view_type',
            ]
        );

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('item', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::__construct
     * @covers \Netgen\BlockManager\View\View\ItemView::getItem
     */
    public function testGetItem(): void
    {
        $this->assertSame($this->item, $this->view->getItem());
        $this->assertSame(
            [
                'item' => $this->item,
                'view_type' => 'view_type',
                'param' => 'value',
            ],
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::getViewType
     */
    public function testGetViewType(): void
    {
        $this->assertSame('view_type', $this->view->getViewType());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        $this->assertSame('item_view', $this->view->getIdentifier());
    }
}
