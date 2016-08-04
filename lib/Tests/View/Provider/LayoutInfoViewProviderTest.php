<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\View\Provider\LayoutInfoViewProvider;
use Netgen\BlockManager\Core\Values\Page\LayoutInfo;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\LayoutInfoViewInterface;
use PHPUnit\Framework\TestCase;

class LayoutInfoViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $layoutInfoViewProvider;

    public function setUp()
    {
        $this->layoutInfoViewProvider = new LayoutInfoViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\LayoutInfoViewProvider::provideView
     */
    public function testProvideView()
    {
        $layout = new LayoutInfo(array('id' => 42));

        /** @var \Netgen\BlockManager\View\View\LayoutInfoViewInterface $view */
        $view = $this->layoutInfoViewProvider->provideView($layout);

        $this->assertInstanceOf(LayoutInfoViewInterface::class, $view);

        $this->assertEquals($layout, $view->getLayout());
        $this->assertNull($view->getTemplate());
        $this->assertEquals(
            array(
                'layout' => $layout,
            ),
            $view->getParameters()
        );
    }

    /**
     * @param \Netgen\BlockManager\API\Values\Value $value
     * @param bool $supports
     *
     * @covers \Netgen\BlockManager\View\Provider\LayoutInfoViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports($value, $supports)
    {
        $this->assertEquals($supports, $this->layoutInfoViewProvider->supports($value));
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(new Value(), false),
            array(new Block(), false),
            array(new LayoutInfo(), true),
        );
    }
}
