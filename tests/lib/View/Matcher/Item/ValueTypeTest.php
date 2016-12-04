<?php

namespace Netgen\BlockManager\Tests\View\Matcher\Item;

use Netgen\BlockManager\Item\Item;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\View\View\ItemView;
use Netgen\BlockManager\View\Matcher\Item\ValueType;
use Netgen\BlockManager\Tests\View\Stubs\View;
use PHPUnit\Framework\TestCase;

class ValueTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    protected $matcher;

    public function setUp()
    {
        $this->matcher = new ValueType();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\Item\ValueType::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $view = new ItemView(array('item' => new Item(array('valueType' => 'value'))));

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
            array(array('other'), false),
            array(array('value'), true),
            array(array('other1', 'other2'), false),
            array(array('other', 'value'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\Item\ValueType::match
     */
    public function testMatchWithNoItemView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }
}
