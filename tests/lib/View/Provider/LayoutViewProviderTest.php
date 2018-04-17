<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\LayoutViewProvider;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use PHPUnit\Framework\TestCase;

final class LayoutViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $layoutViewProvider;

    public function setUp()
    {
        $this->layoutViewProvider = new LayoutViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::provideView
     */
    public function testProvideView()
    {
        $layout = new Layout(['id' => 42]);

        /** @var \Netgen\BlockManager\View\View\LayoutViewInterface $view */
        $view = $this->layoutViewProvider->provideView($layout);

        $this->assertInstanceOf(LayoutViewInterface::class, $view);

        $this->assertEquals($layout, $view->getLayout());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            [
                'layout' => $layout,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $this->assertEquals($supports, $this->layoutViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return [
            [new Value(), false],
            [new Block(), false],
            [new LayoutType(), false],
            [new Layout(), true],
        ];
    }
}
