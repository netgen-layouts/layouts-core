<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\View\View\ItemView;
use PHPUnit\Framework\TestCase;

final class ItemViewTest extends TestCase
{
    private CmsItem $item;

    private ItemView $view;

    protected function setUp(): void
    {
        $this->item = CmsItem::fromArray(
            [
                'value' => 42,
                'valueType' => 'type',
            ],
        );

        $this->view = new ItemView($this->item, 'view_type');

        $this->view->addParameter('param', 'value');
        $this->view->addParameter('item', 42);
    }

    /**
     * @covers \Netgen\Layouts\View\View\ItemView::__construct
     * @covers \Netgen\Layouts\View\View\ItemView::getItem
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
            $this->view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\View\ItemView::getViewType
     */
    public function testGetViewType(): void
    {
        self::assertSame('view_type', $this->view->getViewType());
    }

    /**
     * @covers \Netgen\Layouts\View\View\ItemView::getIdentifier
     */
    public function testGetIdentifier(): void
    {
        self::assertSame('item', $this->view::getIdentifier());
    }
}
