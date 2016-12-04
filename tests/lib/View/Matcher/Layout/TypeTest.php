<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Layout;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Configuration\LayoutType\LayoutType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\Matcher\Layout\Type;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
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
                'layoutType' => new LayoutType(array('identifier' => '4_zones_a')),
            )
        );

        $view = new LayoutView(array('layout' => $layout));

        $this->assertEquals($expected, $this->matcher->match($view, $config));
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
            array(array('4_zones_a'), true),
            array(array('some_type', 'some_type_2'), false),
            array(array('some_type', '4_zones_a'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Type::match
     */
    public function testMatchWithNoLayoutView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }
}
