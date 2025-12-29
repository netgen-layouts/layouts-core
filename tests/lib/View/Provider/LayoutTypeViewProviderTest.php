<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\LayoutTypeViewProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(LayoutTypeViewProvider::class)]
final class LayoutTypeViewProviderTest extends TestCase
{
    private LayoutTypeViewProvider $layoutViewProvider;

    protected function setUp(): void
    {
        $this->layoutViewProvider = new LayoutTypeViewProvider();
    }

    public function testProvideView(): void
    {
        $layoutType = LayoutType::fromArray(['identifier' => 'layout']);

        $view = $this->layoutViewProvider->provideView($layoutType);

        self::assertSame($layoutType, $view->layoutType);
        self::assertNull($view->template);
        self::assertSame(
            [
                'layout_type' => $layoutType,
            ],
            $view->parameters,
        );
    }

    #[DataProvider('supportsDataProvider')]
    public function testSupports(object $value, bool $supports): void
    {
        self::assertSame($supports, $this->layoutViewProvider->supports($value));
    }

    /**
     * @return iterable<mixed>
     */
    public static function supportsDataProvider(): iterable
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new Layout(), false],
            [new LayoutType(), true],
        ];
    }
}
