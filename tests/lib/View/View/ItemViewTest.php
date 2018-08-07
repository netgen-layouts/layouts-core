<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\View\View\ItemView;
use PHPUnit\Framework\TestCase;

final class ItemViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Item\CmsItemInterface
     */
    private $item;

    /**
     * @var \Netgen\BlockManager\View\View\ItemViewInterface
     */
    private $view;

    public function setUp(): void
    {
        $this->item = CmsItem::fromArray(
            [
                'value' => 42,
                'valueType' => 'type',
            ]
        );

        $this->view = new ItemView($this->item, 'view_type');

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('item', 42);
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::__construct
     * @covers \Netgen\BlockManager\View\View\ItemView::getItem
     */
    public function testGetItem(): void
    {
        self::assertSame($this->item, $this->view->getItem());
        self::assertSame(
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
        self::assertSame('view_type', $this->view->getViewType());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\ItemView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('item', $this->view::getIdentifier());
    }
}
