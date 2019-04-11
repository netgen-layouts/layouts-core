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

final class LayoutViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\View\Provider\ViewProviderInterface
     */
    private $layoutViewProvider;

    public function setUp(): void
    {
        $this->layoutViewProvider = new LayoutViewProvider();
    }

    /**
     * @covers \Netgen\Layouts\View\Provider\LayoutViewProvider::provideView
     */
    public function testProvideView(): void
    {
        $layout = Layout::fromArray(['id' => 42]);

        $view = $this->layoutViewProvider->provideView($layout);

        self::assertInstanceOf(LayoutViewInterface::class, $view);

        self::assertSame($layout, $view->getLayout());
        self::assertNull($view->getTemplate());
        self::assertSame(
            [
                'layout' => $layout,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param mixed $value
     * @param bool $supports
     *
     * @covers \Netgen\Layouts\View\Provider\LayoutViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, bool $supports): void
    {
        self::assertSame($supports, $this->layoutViewProvider->supports($value));
    }

    public function supportsProvider(): array
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new LayoutType(), false],
            [new Layout(), true],
        ];
    }
}
