<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\PlaceholderViewProvider;
use Netgen\Layouts\View\View\PlaceholderViewInterface;
use PHPUnit\Framework\TestCase;

final class PlaceholderViewProviderTest extends TestCase
{
    private PlaceholderViewProvider $placeholderViewProvider;

    protected function setUp(): void
    {
        $this->placeholderViewProvider = new PlaceholderViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\PlaceholderViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $block = new Block();
        $placeholder = new Placeholder();

        $view = $this->placeholderViewProvider->provideView(
            $placeholder,
            [
                'block' => $block,
            ],
        );

        self::assertInstanceOf(PlaceholderViewInterface::class, $view);

        self::assertSame($placeholder, $view->getPlaceholder());
        self::assertSame($block, $view->getBlock());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'placeholder' => $placeholder,
                'block' => $block,
            ],
            $view->getParameters(),
        );
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\PlaceholderViewProvider::provideView
     */
    public function testProvideViewThrowsRuntimeExceptionOnMissingBlock(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the placeholder view, "block" parameter needs to be provided.');

        $this->placeholderViewProvider->provideView(new Placeholder());
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\PlaceholderViewProvider::provideView
     */
    public function testProvideViewThrowsRuntimeExceptionOnInvalidBlock(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the placeholder view, "block" parameter needs to be of "Netgen\Layouts\API\Values\Block\Block" type.');

        $this->placeholderViewProvider->provideView(new Placeholder(), ['block' => 42]);
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\PlaceholderViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->placeholderViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new Placeholder(), true],
            [new Block(), false],
        ];
    }
}
