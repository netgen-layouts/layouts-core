<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver;

use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionList;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use Netgen\Layouts\API\Values\LayoutResolver\TargetList;
use Netgen\Layouts\Layout\Resolver\LayoutResolver;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType2;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType3;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType2;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

#[CoversClass(LayoutResolver::class)]
final class LayoutResolverTest extends TestCase
{
    private Stub&LayoutResolverService $layoutResolverServiceStub;

    private TargetTypeRegistry $targetTypeRegistry;

    private LayoutResolver $layoutResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceStub = self::createStub(LayoutResolverService::class);

        $this->layoutResolverServiceStub
            ->method('loadRuleGroups')
            ->willReturn(new RuleGroupList());

        $this->targetTypeRegistry = new TargetTypeRegistry([]);
        $this->createLayoutResolver();
    }

    public function testResolveRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42), new TargetType2(84)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 2,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 4,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule3 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 5,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule4 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 4,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->willReturnMap(
                [
                    [$ruleGroup, 'target1', 42, RuleList::fromArray([$rule1, $rule2])],
                    [$ruleGroup, 'target2', 84, RuleList::fromArray([$rule3, $rule4])],
                ],
            );

        $resolvedRules = $this->layoutResolver->resolveRules(Request::create('/'));

        self::assertCount(4, $resolvedRules);

        // We can't be sure in what order two rules with same priority will be returned,
        // so just assert the first one and the last one
        self::assertSame($rule3, $resolvedRules[0]);
        self::assertSame($rule1, $resolvedRules[3]);
    }

    public function testResolveRulesWithInvalidRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 2,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule1, $rule2]));

        self::assertSame([$rule1], $this->layoutResolver->resolveRules(Request::create('/')));
    }

    public function testResolveRulesWithDisabledRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 2,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 4,
                'isEnabled' => false,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule1, $rule2]));

        self::assertSame([$rule1], $this->layoutResolver->resolveRules(Request::create('/')));
    }

    public function testResolveRulesWithNoValidRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 2,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule1, $rule2]));

        self::assertSame([], $this->layoutResolver->resolveRules(Request::create('/')));
    }

    public function testResolveRulesWithNoTargetValue(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(),
                new TargetType2(84),
            ],
        );

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 5,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 7,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target2'), self::identicalTo(84))
            ->willReturn(RuleList::fromArray([$rule1, $rule2]));

        self::assertSame([$rule2, $rule1], $this->layoutResolver->resolveRules(Request::create('/')));
    }

    public function testResolveRulesWithNoTargetValues(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(),
                new TargetType2(),
            ],
        );

        $this->createLayoutResolver();

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        self::assertSame([], $this->layoutResolver->resolveRules(Request::create('/')));
    }

    /**
     * @param string[] $conditionTypes
     */
    #[DataProvider('resolveRulesWithPartialRuleConditionsDataProvider')]
    public function testResolveRulesWithConditionsAndPartialConditionMatching(array $conditionTypes, bool $resolved): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['value' => 42, 'conditionType' => $conditionType]);
        }

        $rule1 = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::v7()]) : null,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'priority' => 4,
                'conditions' => ConditionList::fromArray([RuleCondition::fromArray(['value' => 42, 'conditionType' => new ConditionType2(false)])]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::v7()]) : null,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'priority' => 2,
                'conditions' => ConditionList::fromArray($conditions),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule1, $rule2]));

        self::assertSame(
            $resolved ? [$rule2] : [],
            $this->layoutResolver->resolveRules(Request::create('/'), ['condition2']),
        );
    }

    /**
     * @param string[] $conditionTypes
     */
    #[DataProvider('resolveRulesWithRuleConditionsDataProvider')]
    public function testResolveRulesWithConditions(array $conditionTypes, bool $resolved): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['value' => 42, 'conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::v7()]) : null,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray($conditions),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule]));

        self::assertSame(
            $resolved ? [$rule] : [],
            $this->layoutResolver->resolveRules(Request::create('/')),
        );
    }

    public function testResolveRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(42),
                new TargetType2(84),
            ],
        );

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 2,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 4,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule3 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 5,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule4 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 4,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->willReturnMap(
                [
                    [$ruleGroup, 'target1', 42, RuleList::fromArray([$rule1, $rule2])],
                    [$ruleGroup, 'target2', 84, RuleList::fromArray([$rule3, $rule4])],
                ],
            );

        self::assertSame($rule3, $this->layoutResolver->resolveRule(Request::create('/')));
    }

    public function testResolveRuleWithInvalidRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 2,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule1, $rule2]));

        self::assertSame($rule1, $this->layoutResolver->resolveRule(Request::create('/')));
    }

    public function testResolveRuleWithNoValidRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 2,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule1, $rule2]));

        self::assertNull($this->layoutResolver->resolveRule(Request::create('/')));
    }

    public function testResolveRuleWithNoTargetValue(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(),
                new TargetType2(84),
            ],
        );

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 5,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::v7()]),
                'priority' => 7,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray([]),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target2'), self::identicalTo(84))
            ->willReturn(RuleList::fromArray([$rule1, $rule2]));

        self::assertSame($rule2, $this->layoutResolver->resolveRule(Request::create('/')));
    }

    public function testResolveRuleWithNoTargetValues(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(),
                new TargetType2(),
            ],
        );

        $this->createLayoutResolver();

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        self::assertNull($this->layoutResolver->resolveRule(Request::create('/')));
    }

    /**
     * @param string[] $conditionTypes
     */
    #[DataProvider('resolveRulesWithPartialRuleConditionsDataProvider')]
    public function testResolveRuleWithConditionsAndPartialConditionMatching(array $conditionTypes, bool $resolved): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['value' => 42, 'conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::v7()]) : null,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray($conditions),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule]));

        self::assertSame(
            $resolved ? $rule : null,
            $this->layoutResolver->resolveRule(
                Request::create('/'),
                ['condition2'],
            ),
        );
    }

    /**
     * @param string[] $conditionTypes
     */
    #[DataProvider('resolveRulesWithRuleConditionsDataProvider')]
    public function testResolveRuleWithConditions(array $conditionTypes, bool $resolved): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['value' => 42, 'conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::v7()]) : null,
                'isEnabled' => true,
                'targets' => TargetList::fromArray([]),
                'conditions' => ConditionList::fromArray($conditions),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceStub
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceStub
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(RuleList::fromArray([$rule]));

        self::assertSame($resolved ? $rule : null, $this->layoutResolver->resolveRule(Request::create('/')));
    }

    /**
     * @return iterable<mixed>
     */
    public static function resolveRulesWithRuleConditionsDataProvider(): iterable
    {
        return [
            [[], true],
            [[new ConditionType3(true)], true],
            [[new ConditionType3(false)], false],
            [[new ConditionType1(true), new ConditionType2(false)], false],
            [[new ConditionType1(false), new ConditionType2(true)], false],
            [[new ConditionType1(false), new ConditionType2(false)], false],
            [[new ConditionType1(true), new ConditionType2(true)], true],
        ];
    }

    /**
     * @return iterable<mixed>
     */
    public static function resolveRulesWithPartialRuleConditionsDataProvider(): iterable
    {
        return [
            [[], true],
            [[new ConditionType3(true)], true],
            [[new ConditionType3(false)], true],
            [[new ConditionType1(true), new ConditionType2(false)], false],
            [[new ConditionType1(false), new ConditionType2(true)], true],
            [[new ConditionType1(false), new ConditionType2(false)], false],
            [[new ConditionType1(true), new ConditionType2(true)], true],
        ];
    }

    private function createLayoutResolver(): void
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceStub,
            $this->targetTypeRegistry,
        );
    }
}
