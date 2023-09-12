<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\RuleCondition;

use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\RuleCondition\Type;
use Netgen\Layouts\View\View\RuleConditionView;
use PHPUnit\Framework\TestCase;

final class TypeTest extends TestCase
{
    private Type $matcher;

    protected function setUp(): void
    {
        $this->matcher = new Type();
    }

    /**
     * @param mixed[] $config
     *
     * @covers \Netgen\Layouts\View\Matcher\RuleCondition\Type::match
     *
     * @dataProvider matchDataProvider
     */
    public function testMatch(array $config, bool $expected): void
    {
        $condition = RuleCondition::fromArray(
            [
                'conditionType' => new ConditionType1(),
            ],
        );

        $view = new RuleConditionView($condition);

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\RuleCondition\Type::match
     */
    public function testMatchWithNullConditionType(): void
    {
        $condition = RuleCondition::fromArray(
            [
                'conditionType' => new NullConditionType(),
            ],
        );

        $view = new RuleConditionView($condition);

        self::assertTrue($this->matcher->match($view, ['null']));
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\RuleCondition\Type::match
     */
    public function testMatchWithNullConditionTypeReturnsFalse(): void
    {
        $condition = RuleCondition::fromArray(
            [
                'conditionType' => new NullConditionType(),
            ],
        );

        $view = new RuleConditionView($condition);

        self::assertFalse($this->matcher->match($view, ['test']));
    }

    public static function matchDataProvider(): iterable
    {
        return [
            [[], false],
            [['other_type'], false],
            [['condition1'], true],
            [['other_type', 'other_type_2'], false],
            [['other_type', 'condition1'], true],
        ];
    }

    /**
     * @covers \Netgen\Layouts\View\Matcher\RuleCondition\Type::match
     */
    public function testMatchWithNoRuleConditionView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
