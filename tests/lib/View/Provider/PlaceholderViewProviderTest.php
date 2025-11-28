<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Exception\View\ViewProviderException;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\PlaceholderViewProvider;
use Netgen\Layouts\View\View\PlaceholderViewInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(PlaceholderViewProvider::class)]
final class PlaceholderViewProviderTest extends TestCase
{
    private PlaceholderViewProvider $placeholderViewProvider;

    protected function setUp(): void
    {
        $this->placeholderViewProvider = new PlaceholderViewProvider();
    }

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

        self::assertSame($placeholder, $view->placeholder);
        self::assertSame($block, $view->block);
        self::assertNull($view->template);
        self::assertSame(
            [
                'placeholder' => $placeholder,
                'block' => $block,
            ],
            $view->parameters,
        );
    }

    public function testProvideViewThrowsRuntimeExceptionOnMissingBlock(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the placeholder view, "block" parameter needs to be provided.');

        $this->placeholderViewProvider->provideView(new Placeholder());
    }

    public function testProvideViewThrowsRuntimeExceptionOnInvalidBlock(): void
    {
        $this->expectException(ViewProviderException::class);
        $this->expectExceptionMessage('To build the placeholder view, "block" parameter needs to be of "Netgen\Layouts\API\Values\Block\Block" type.');

        $this->placeholderViewProvider->provideView(new Placeholder(), ['block' => 42]);
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(mixed $value, bool $supports): void
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
