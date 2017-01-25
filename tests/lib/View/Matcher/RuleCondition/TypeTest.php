<?php

namespace Netgen\BlockManager\Tests\View\Matcher\RuleCondition;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\RuleCondition\Type;
use Netgen\BlockManager\View\View\RuleConditionView;
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
     * @covers \Netgen\BlockManager\View\Matcher\RuleCondition\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $condition = new Condition(
            array(
                'conditionType' => new ConditionType('route_parameter'),
            )
        );

        $view = new RuleConditionView(array('condition' => $condition));

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
            array(array('other_type'), false),
            array(array('route_parameter'), true),
            array(array('other_type', 'other_type_2'), false),
            array(array('other_type', 'route_parameter'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleCondition\Type::match
     */
    public function testMatchWithNoRuleConditionView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }
}