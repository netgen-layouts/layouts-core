<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Layout\Type\LayoutType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\LayoutTypeViewProvider;
use Netgen\BlockManager\View\View\LayoutTypeViewInterface;
use PHPUnit\Framework\TestCase;

final class LayoutTypeViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    private $layoutViewProvider;

    public function setUp()
    {
        $this->layoutViewProvider = new LayoutTypeViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\LayoutTypeViewProvider::provideView
     */
    public function testProvideView()
    {
        $layoutType = new LayoutType(['identifier' => 'layout']);

        /** @var \Netgen\BlockManager\View\View\LayoutTypeViewInterface $view */
        $view = $this->layoutViewProvider->provideView($layoutType);

        $this->assertInstanceOf(LayoutTypeViewInterface::class, $view);

        $this->assertEquals($layoutType, $view->getLayoutType());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            [
                'layoutType' => $layoutType,
            ],
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\LayoutTypeViewProvider::supports
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
            [new Layout(), false],
            [new LayoutType(), true],
        ];
    }
}
