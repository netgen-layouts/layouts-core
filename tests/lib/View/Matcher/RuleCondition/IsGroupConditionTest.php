<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Matcher\RuleCondition;

use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\Matcher\RuleCondition\IsGroupCondition;
use Netgen\Layouts\View\View\RuleConditionView;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IsGroupCondition::class)]
final class IsGroupConditionTest extends TestCase
{
    private IsGroupCondition $matcher;

    protected function setUp(): void
    {
        $this->matcher = new IsGroupCondition();
    }

    /**
     * @param mixed[] $config
     */
    #[DataProvider('matchWithRuleConditionDataProvider')]
    public function testMatchWithRuleCondition(array $config, bool $expected): void
    {
        $view = new RuleConditionView(new RuleCondition());

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public static function matchWithRuleConditionDataProvider(): iterable
    {
        return [
            [[], false],
            [[true], false],
            [[false], true],
            [[true, false], true],
            [[42], false],
        ];
    }

    /**
     * @param mixed[] $config
     */
    #[DataProvider('matchWithRuleGroupConditionDataProvider')]
    public function testMatchWithRuleGroupCondition(array $config, bool $expected): void
    {
        $view = new RuleConditionView(new RuleGroupCondition());

        self::assertSame($expected, $this->matcher->match($view, $config));
    }

    public static function matchWithRuleGroupConditionDataProvider(): iterable
    {
        return [
            [[], false],
            [[true], true],
            [[false], false],
            [[true, false], true],
            [[42], false],
        ];
    }

    public function testMatchWithNoRuleConditionView(): void
    {
        self::assertFalse($this->matcher->match(new View(new Value()), []));
    }
}
