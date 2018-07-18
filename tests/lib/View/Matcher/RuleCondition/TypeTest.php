<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Matcher\RuleCondition;

use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\BlockManager\Tests\Core\Stubs\Value;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\View\Stubs\View;
use Netgen\BlockManager\View\Matcher\RuleCondition\Type;
use Netgen\BlockManager\View\View\RuleConditionView;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\View\Matcher\MatcherInterface
     */
    private $matcher;

    public function setUp(): void
    {
        $this->matcher = new Type();
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleCondition\Type::match
     * @dataProvider matchProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $condition = Condition::fromArray(
            [
                'conditionType' => new ConditionType('route_parameter'),
            ]
        );

        $view = new RuleConditionView($condition);

        $this->assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleCondition\Type::match
     */
    public function testMatchWithNullConditionType(): void
    {
        $condition = Condition::fromArray(
            [
                'conditionType' => new NullConditionType('type'),
            ]
        );

        $view = new RuleConditionView($condition);

        $this->assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleCondition\Type::match
     */
    public function testMatchWithNullConditionTypeReturnsFalse(): void
    {
        $condition = Condition::fromArray(
            [
                'conditionType' => new NullConditionType('type'),
            ]
        );

        $view = new RuleConditionView($condition);

        $this->assertFalse($this->matcher->match($view, ['test']));
    }

    public function matchProvider(): array
    {
        return [
            [[], false],
            [['other_type'], false],
            [['route_parameter'], true],
            [['other_type', 'other_type_2'], false],
            [['other_type', 'route_parameter'], true],
        ];
    }

    /**
     * @covers \Netgen\BlockManager\View\Matcher\RuleCondition\Type::match
     */
    public function testMatchWithNoRuleConditionView(): void
    {
        $this->assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
