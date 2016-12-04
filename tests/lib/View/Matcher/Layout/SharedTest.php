<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Layout;

use Netgen\BlockManager\Core\Values\Page\Layout;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\Layout\Shared;
use Netgen\BlockManager\View\View\LayoutView;
use PHPUnit\Framework\TestCase;

class SharedTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new Shared();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Shared::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $layout = new Layout(
            array(
                'shared' => true,
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
            array(array(), true),
            array(array(true), true),
            array(array(false), false),
            array(array('something_else'), false),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Layout\Shared::match
     */
    public function testMatchWithNoLayoutView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }
}
