<?php

namespace Netgen\BlockManager\View\Tests\Builder;

use Netgen\BlockManager\View\Builder\LayoutViewBuilder;
use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\API\Tests\Stubs\Value;
use PHPUnit_Framework_TestCase;

class LayoutViewBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Builder\LayoutViewBuilder::buildView
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewThrowsInvalidArgumentException()
    {
        $layoutViewBuilder = new LayoutViewBuilder(
            $this->getMock('Netgen\BlockManager\API\Service\BlockService')
        );

        $layoutViewBuilder->buildView(new Value());
    }

    /**
     * @covers \Netgen\BlockManager\View\Builder\LayoutViewBuilder::buildView
     */
    public function testBuildView()
    {
        $layout = new Layout(array('id' => 42));

        $blockServiceMock = $this
            ->getMock('Netgen\BlockManager\API\Service\BlockService');

        $layoutBlocks = array(new Block(array('id' => 42)));
        $blockServiceMock
            ->expects($this->once())
            ->method('loadLayoutBlocks')
            ->will($this->returnValue($layoutBlocks));

        $layoutViewBuilder = new LayoutViewBuilder($blockServiceMock);

        /** @var \Netgen\BlockManager\View\LayoutViewInterface $view */
        $view = $layoutViewBuilder->buildView(
            $layout,
            array('some_param' => 'some_value'),
            'manager'
        );

        self::assertInstanceOf('Netgen\BlockManager\View\LayoutViewInterface', $view);

        self::assertEquals($layout, $view->getLayout());
        self::assertEquals('manager', $view->getContext());
        self::assertEquals(null, $view->getTemplate());
        self::assertEquals(
            array(
                'layout' => $layout,
                'some_param' => 'some_value',
                'blocks' => $layoutBlocks
            ),
            $view->getParameters()
        );
    }
}
