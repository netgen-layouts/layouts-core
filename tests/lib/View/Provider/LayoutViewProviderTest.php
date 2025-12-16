<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\LayoutViewProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutViewProvider::class)]
final class LayoutViewProviderTest extends TestCase
{
    private LayoutViewProvider $layoutViewProvider;

    protected function setUp(): void
    {
        $this->layoutViewProvider = new LayoutViewProvider();
    }

    public function testProvideView(): void
    {
        $layout = Layout::fromArray(['id' => Uuid::v4()]);

        $view = $this->layoutViewProvider->provideView($layout);

        self::assertSame($layout, $view->layout);
        self::assertNull($view->template);
        self::assertSame(
            [
                'layout' => $layout,
            ],
            $view->parameters,
        );
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(object $value, bool $supports): void
    {
        self::assertSame($supports, $this->layoutViewProvider->supports($value));
    }

    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new LayoutType(), false],
            [new Layout(), true],
        ];
    }
}
