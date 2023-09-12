<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\LayoutTypeViewProvider;
use Netgen\Layouts\View\View\LayoutTypeViewInterface;
use PHPUnit\Framework\TestCase;

final class LayoutTypeViewProviderTest extends TestCase
{
    private LayoutTypeViewProvider $layoutViewProvider;

    protected function setUp(): void
    {
        $this->layoutViewProvider = new LayoutTypeViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\LayoutTypeViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $layoutType = LayoutType::fromArray(['identifier' => 'layout']);

        $view = $this->layoutViewProvider->provideView($layoutType);

        self::assertInstanceOf(LayoutTypeViewInterface::class, $view);

        self::assertSame($layoutType, $view->getLayoutType());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'layout_type' => $layoutType,
            ],
            $view->getParameters(),
        );
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\LayoutTypeViewProvider::supports
     *
     * @dataProvider supportsDataProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->layoutViewProvider->supports($value));
    }

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
