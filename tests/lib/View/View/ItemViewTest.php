<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\View;

use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\View\View\ItemView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ItemView::class)]
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

    public function testGetItem(): void
    {
        self::assertSame($this->item, $this->view->item);
        self::assertSame(
            [
                'param' => 'value',
                'item' => $this->item,
                'view_type' => 'view_type',
            ],
            $this->view->parameters,
        );
    }

    public function testGetViewType(): void
    {
        self::assertSame('view_type', $this->view->viewType);
    }

    public function testGetIdentifier(): void
    {
        self::assertSame('item', $this->view->identifier);
    }
}
