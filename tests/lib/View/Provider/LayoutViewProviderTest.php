<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\Provider\LayoutViewProvider;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use PHPUnit\Framework\TestCase;

class LayoutViewProviderTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Provider\ViewProviderInterface
     */
    protected $layoutViewProvider;

    public function setUp()
    {
        $this->layoutViewProvider = new LayoutViewProvider();
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::provideView
     */
    public function testProvideView()
    {
        $layout = new Layout(array('id' => 42));

        /** @var \Netgen\BlockManager\View\View\LayoutViewInterface $view */
        $view = $this->layoutViewProvider->provideView($layout);

        $this->assertInstanceOf(LayoutViewInterface::class, $view);

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
        return array(
            array(new Value(), false),
            array(new Block(), false),
            array(new Layout(), true),
        );
    }
}
