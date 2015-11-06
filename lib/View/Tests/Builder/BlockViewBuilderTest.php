<?php

namespace Netgen\BlockManager\View\Tests\Builder;

use Netgen\BlockManager\View\Builder\BlockViewBuilder;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\API\Tests\Stubs\Value;
use PHPUnit_Framework_TestCase;

class BlockViewBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Builder\BlockViewBuilder::buildView
     * @expectedException \InvalidArgumentException
     */
    public function testBuildViewThrowsInvalidArgumentException()
    {
        $blockViewBuilder = new BlockViewBuilder();
        $blockViewBuilder->buildView(new Value());
    }

    /**
     * @covers \Netgen\BlockManager\View\Builder\BlockViewBuilder::buildView
     */
    public function testBuildView()
    {
        $block = new Block(array('id' => 42));

        $blockViewBuilder = new BlockViewBuilder();

        /** @var \Netgen\BlockManager\View\BlockViewInterface $view */
        $view = $blockViewBuilder->buildView(
            $block,
            array('some_param' => 'some_value'),
            'manager'
        );

        self::assertInstanceOf('Netgen\BlockManager\View\BlockViewInterface', $view);

        self::assertEquals($block, $view->getBlock());
        self::assertEquals('manager', $view->getContext());
        self::assertEquals(null, $view->getTemplate());
        self::assertEquals(
            array(
                'block' => $block,
                'some_param' => 'some_value'
            ),
            $view->getParameters()
        );
    }
}
