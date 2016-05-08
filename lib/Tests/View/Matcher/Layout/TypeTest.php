<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Layout;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\Matcher\Layout\Type;
use Netgen\BlockManager\Tests\View\Stubs\View;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     * @covers \Netgen\BlockManager\View\Matcher\Matcher::setConfig
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $matcher = new Type();
        $matcher->setConfig($config);

        $layout = new Layout(
            array(
                'type' => '3_zones_a',
            )
        );

        $view = new LayoutView();
        $view->setLayout($layout);

        self::assertEquals($expected, $matcher->match($view));
    }

    /**
     * Provider for {@link self::testMatch}.
     *
     * @return array
     */
    public function matchProvider()
    {
        return array(
            array(array(), false),
            array(array('some_type'), false),
            array(array('3_zones_a'), true),
            array(array('some_type', 'some_type_2'), false),
            array(array('some_type', '3_zones_a'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNoLayoutView()
    {
        $matcher = new Type();
        self::assertFalse($matcher->match(new View()));
    }
}
