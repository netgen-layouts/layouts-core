<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Item\CmsItem;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\ItemViewProvider;
use Netgen\Layouts\View\View\ItemViewInterface;
use PHPUnit\Framework\TestCase;

final class ItemViewProviderTest extends TestCase
{
    private ItemViewProvider $itemViewProvider;

    protected function setUp(): void
    {
        $this->itemViewProvider = new ItemViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\ItemViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $item = new CmsItem();

        $view = $this->itemViewProvider->provideView($item, ['view_type' => 'view_type']);

        self::assertInstanceOf(ItemViewInterface::class, $view);

        self::assertSame($item, $view->getItem());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'item' => $item,
                'view_type' => 'view_type',
            ],
            $view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\ItemViewProvider::provideView
     */
    public function testProvideViewThrowsViewProviderExceptionOnMissingViewType(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the item view, "view_type" parameter needs to be provided.');

        $this->itemViewProvider->provideView(new CmsItem());
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\ItemViewProvider::provideView
     */
    public function testProvideViewThrowsViewProviderExceptionOnInvalidViewType(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the item view, "view_type" parameter needs to be of "string" type.');

        $this->itemViewProvider->provideView(new CmsItem(), ['view_type' => 42]);
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\ItemViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->itemViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new CmsItem(), true],
            [new Layout(), false],
        ];
    }
}
