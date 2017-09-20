<?php

namespace Netgen\BlockManager\Tests\View\Matcher\RuleTarget;

use Netgen\BlockManager\Core\Values\LayoutResolver\Target;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\RuleTarget\Type;
use Netgen\BlockManager\View\View\RuleTargetView;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new Type();
    }

    /**
     * @param array $config
     * @param bool $expected
     *
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, $expected)
    {
        $target = new Target(
            array(
                'targetType' => new TargetType('route'),
            )
        );

        $view = new RuleTargetView(array('target' => $target));

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
            array(array('route'), true),
            array(array('some_type', 'some_type_2'), false),
            array(array('some_type', 'route'), true),
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleTarget\Type::match
     */
    public function testMatchWithNoRuleTargetView()
    {
        $this->assertFalse($this->matcher->match(new View(array('value' => new Value())), array()));
    }
}
