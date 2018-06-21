<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Item\CmsItem;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\ItemViewProvider;
use Netgen\BlockManager\View\View\ItemViewInterface;
use PHPUnit\Framework\TestCase;

final class ItemViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $itemViewProvider;

    public function setUp(): void
    {
        $this->itemViewProvider = new ItemViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $item = new CmsItem();

        /** @var \Netgen\BlockManager\View\View\ItemViewInterface $view */
        $view = $this->itemViewProvider->provideView($item, ['view_type' => 'view_type']);

        $this->assertInstanceOf(ItemViewInterface::class, $view);

        $this->assertSame($item, $view->getItem());
        $this->assertNull($view->getTemplate());
        $this->assertSame(
            [
                'item' => $item,
                'view_type' => 'view_type',
            ],
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage To build the item view, "view_type" parameter needs to be provided.
     */
    public function testProvideViewThrowsViewProviderExceptionOnMissingViewType(): void
    {
        $this->itemViewProvider->provideView(new CmsItem());
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::provideView
     * @expectedException \Netgen\BlockManager\Exception\View\ViewProviderException
     * @expectedExceptionMessage To build the item view, "view_type" parameter needs to be of "string" type.
     */
    public function testProvideViewThrowsViewProviderExceptionOnInvalidViewType(): void
    {
        $this->itemViewProvider->provideView(new CmsItem(), ['view_type' => 42]);
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\ItemViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        $this->assertSame($supports, $this->itemViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new CmsItem(), true],
            [new Layout(), false],
        ];
    }
}
