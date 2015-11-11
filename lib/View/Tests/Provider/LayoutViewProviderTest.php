<?php

namespace Netgen\BlockManager\View\Tests\Provider;

use Netgen\BlockManager\View\Provider\LayoutViewProvider;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\API\Tests\Stubs\Value;
use PHPUnit_Framework_TestCase;

class LayoutViewProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::__construct
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::provideView
     */
    public function testProvideView()
    {
        $layout = new Layout(array('id' => 42));

        $blockServiceMock = $this
            ->getMock('Netgen\BlockManager\API\Service\BlockService');

        $layoutBlocks = array(new Block(array('id' => 42)));
        $blockServiceMock
            ->expects($this->once())
            ->method('loadLayoutBlocks')
            ->will($this->returnValue($layoutBlocks));

        $layoutViewProvider = new LayoutViewProvider($blockServiceMock);

        /** @var \Netgen\BlockManager\View\LayoutViewInterface $view */
        $view = $layoutViewProvider->provideView(
            $layout,
            array('some_param' => 'some_value'),
            'api'
        );

        self::assertInstanceOf('Netgen\BlockManager\View\LayoutViewInterface', $view);

        self::assertEquals($layout, $view->getLayout());
        self::assertEquals('api', $view->getContext());
        self::assertEquals(null, $view->getTemplate());
        self::assertEquals(
            array(
                'layout' => $layout,
                'some_param' => 'some_value',
                'blocks' => $layoutBlocks,
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\LayoutViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports()
    {
        $blockServiceMock = $this
            ->getMock('Netgen\BlockManager\API\Service\BlockService');

        $layoutViewProvider = new LayoutViewProvider($blockServiceMock);
        $layoutViewProvider->supports(new Value());
    }

    /**
     * Provider for {@link self::testSupports}.
     *
     * @return array
     */
    public function supportsProvider()
    {
        return array(
            array(null, false),
            array(true, false),
            array(false, false),
            array('block', false),
            array(array(), false),
            array(42, false),
            array(42.12, false),
            array(new Value(), false),
            array(new Block(), true),
        );
    }
}
