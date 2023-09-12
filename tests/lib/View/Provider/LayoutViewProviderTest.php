<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Provider;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Layout\Type\LayoutType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\View\Provider\LayoutViewProvider;
use Netgen\Layouts\View\View\LayoutViewInterface;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LayoutViewProviderTest extends TestCase
{
    private LayoutViewProvider $layoutViewProvider;

    protected function setUp(): void
    {
        $this->layoutViewProvider = new LayoutViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\LayoutViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $layout = Layout::fromArray(['id' => Uuid::uuid4()]);

        $view = $this->layoutViewProvider->provideView($layout);

        self::assertInstanceOf(LayoutViewInterface::class, $view);

        self::assertSame($layout, $view->getLayout());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'layout' => $layout,
            ],
            $view->getParameters(),
        );
    }

    /**
     * @param mixed $value
     *
     * @covers \Netgen\Layouts\View\Provider\LayoutViewProvider::supports
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
            [new LayoutType(), false],
            [new Layout(), true],
        ];
    }
}
