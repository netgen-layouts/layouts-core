<?php

namespace Netgen\BlockManager\View\Tests\Provider;

use Netgen\BlockManager\View\Provider\BlockViewProvider;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\API\Tests\Stubs\Value;
use PHPUnit_Framework_TestCase;

class BlockViewProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::provideView
     */
    public function testProvideView()
    {
        $block = new Block(array('id' => 42));

        $blockViewProvider = new BlockViewProvider();

        /** @var \Netgen\BlockManager\View\BlockViewInterface $view */
        $view = $blockViewProvider->provideView(
            $block,
            array('some_param' => 'some_value'),
            'api'
        );

        self::assertInstanceOf('Netgen\BlockManager\View\BlockViewInterface', $view);

        self::assertEquals($block, $view->getBlock());
        self::assertEquals('api', $view->getContext());
        self::assertEquals(null, $view->getTemplate());
        self::assertEquals(
            array(
                'block' => $block,
                'some_param' => 'some_value',
            ),
            $view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Provider\BlockViewProvider::supports
     * @dataProvider supportsProvider
     */
    public function testSupports()
    {
        $blockViewProvider = new BlockViewProvider();
        $blockViewProvider->supports(new Value());
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
