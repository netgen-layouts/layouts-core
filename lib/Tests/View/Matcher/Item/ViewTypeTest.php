<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\ItemView;
use Netgen\BlockManager\View\Matcher\Item\ViewType;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

class ViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new ViewType();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Item\ViewType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $view = new ItemView(new Item(), 'view_type');

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
            array(array('other'), false),
            array(array('view_type'), true),
            array(array('other1', 'other2'), false),
            array(array('other', 'view_type'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ViewType::match
     */
    public function testMatchWithNoItemView()
    {
        self::assertFalse($this->matcher->match(new View(new Value()), array()));
    }
}
