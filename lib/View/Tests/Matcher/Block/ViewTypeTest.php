<?php

namespace Netgen\BlockManager\View\Tests\Matcher\Block;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\View\BlockView;
use Netgen\BlockManager\View\Matcher\Block\ViewType;
use Netgen\BlockManager\View\Tests\Stubs\View;
use PHPUnit_Framework_TestCase;

class ViewTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Block\ViewType::match
     * @covers \Netgen\BlockManager\View\Matcher\Matcher::setConfig
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $matcher = new ViewType();
        $matcher->setConfig($config);

        $block = new Block(
            array(
                'viewType' => 'default'
            )
        );

        $view = new BlockView();
        $view->setBlock($block);

        self::assertEquals($expected, $matcher->match($view));
    }

    /**
     * Provider for {@link self::testMatch}
     *
     * @return array
     */
    public function matchProvider()
    {
        return array(
            array(array(), false),
            array(array('small'), false),
            array(array('default'), true),
            array(array('small', 'large'), false),
            array(array('small', 'default'), true)
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Block\ViewType::match
     */
    public function testMatchWithNoBlockView()
    {
        $matcher = new ViewType();
        self::assertEquals(false, $matcher->match(new View()));
    }
}
