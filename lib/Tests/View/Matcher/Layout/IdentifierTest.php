<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Layout;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\Matcher\Layout\Identifier;
use Netgen\BlockManager\Tests\View\Stubs\View;

class IdentifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Identifier::match
     * @covers \Netgen\BlockManager\View\Matcher\Matcher::setConfig
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $matcher = new Identifier();
        $matcher->setConfig($config);

        $layout = new Layout(
            array(
                'identifier' => '3_zones_a',
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
            array(array('some_identifier'), false),
            array(array('3_zones_a'), true),
            array(array('some_identifier', 'some_identifier_2'), false),
            array(array('some_identifier', '3_zones_a'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Identifier::match
     */
    public function testMatchWithNoLayoutView()
    {
        $matcher = new Identifier();
        self::assertEquals(false, $matcher->match(new View()));
    }
}
