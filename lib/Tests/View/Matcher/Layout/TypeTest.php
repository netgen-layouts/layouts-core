<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Layout;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\LayoutView;
use Netgen\BlockManager\View\Matcher\Layout\Type;
use Netgen\BlockManager\Tests\View\Stubs\View;

class TypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new Type();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $layout = new Layout(
            array(
                'type' => '3_zones_a',
            )
        );

        $view = new LayoutView($layout);

        self::assertEquals($expected, $this->matcher->match($view, $config));
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
        self::assertFalse($this->matcher->match(new View(new Value()), array()));
    }
}
