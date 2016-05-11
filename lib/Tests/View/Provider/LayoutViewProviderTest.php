<?php

namespace Netgen\BlockManager\Tests\View\Provider;

use Netgen\BlockManager\View\Provider\LayoutViewProvider;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\API\Stubs\Value;
use Netgen\BlockManager\View\LayoutViewInterface;

class LayoutViewProviderTest extends \PHPUnit_Framework_TestCase
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

        /** @var \Netgen\BlockManager\View\LayoutViewInterface $view */
        $view = $this->layoutViewProvider->provideView($layout);

        self::assertInstanceOf(LayoutViewInterface::class, $view);

        self::assertEquals($layout, $view->getLayout());
        self::assertNull($view->getTemplate());
        self::assertEquals(
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
        self::assertEquals($supports, $this->layoutViewProvider->supports($value));
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
