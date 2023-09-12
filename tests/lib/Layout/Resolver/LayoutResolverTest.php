<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Layout\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Layouts\API\Service\LayoutResolverService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use Netgen\Layouts\Layout\Resolver\LayoutResolver;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType2;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType3;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\TargetType2;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class LayoutResolverTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\Layouts\API\Service\LayoutResolverService
     */
    private MockObject $layoutResolverServiceMock;

    private TargetTypeRegistry $targetTypeRegistry;

    private RequestStack $requestStack;

    private LayoutResolver $layoutResolver;

    protected function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->layoutResolverServiceMock
            ->method('loadRuleGroups')
            ->willReturn(new RuleGroupList());

        $this->requestStack = new RequestStack();
        $this->requestStack->push(Request::create('/'));

        $this->targetTypeRegistry = new TargetTypeRegistry([]);
        $this->createLayoutResolver();
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::__construct
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42), new TargetType2(84)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule3 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule4 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->willReturnMap(
                [
                    [$ruleGroup, 'target1', 42, new RuleList([$rule1, $rule2])],
                    [$ruleGroup, 'target2', 84, new RuleList([$rule3, $rule4])],
                ],
            );

        $resolvedRules = $this->layoutResolver->resolveRules($this->requestStack->getCurrentRequest());

        self::assertCount(4, $resolvedRules);

        // We can't be sure in what order two rules with same priority will be returned,
        // so just assert the first one and the last one
        self::assertSame($rule3, $resolvedRules[0]);
        self::assertSame($rule1, $resolvedRules[3]);
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithInvalidRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame([$rule1], $this->layoutResolver->resolveRules($this->requestStack->getCurrentRequest()));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithDisabledRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 4,
                'enabled' => false,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame([$rule1], $this->layoutResolver->resolveRules($this->requestStack->getCurrentRequest()));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoValidRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame([], $this->layoutResolver->resolveRules($this->requestStack->getCurrentRequest()));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRules
     */
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
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 7,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target2'), self::identicalTo(84))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame([$rule2, $rule1], $this->layoutResolver->resolveRules($this->requestStack->getCurrentRequest()));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRules
     */
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

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('matchRules');

        self::assertSame([], $this->layoutResolver->resolveRules($this->requestStack->getCurrentRequest()));
    }

    /**
     * @param string[] $conditionTypes
     *
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::conditionsMatch
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRules
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsDataProvider
     */
    public function testResolveRulesWithConditionsAndPartialConditionMatching(array $conditionTypes, bool $resolved): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['conditionType' => $conditionType]);
        }

        $rule1 = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::uuid4()]) : null,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'priority' => 4,
                'conditions' => new ArrayCollection([RuleCondition::fromArray(['conditionType' => new ConditionType2(false)])]),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::uuid4()]) : null,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'priority' => 2,
                'conditions' => new ArrayCollection($conditions),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame(
            $resolved ? [$rule2] : [],
            $this->layoutResolver->resolveRules($this->requestStack->getCurrentRequest(), ['condition2']),
        );
    }

    /**
     * @param string[] $conditionTypes
     *
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::conditionsMatch
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRules
     *
     * @dataProvider resolveRulesWithRuleConditionsDataProvider
     */
    public function testResolveRulesWithConditions(array $conditionTypes, bool $resolved): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::uuid4()]) : null,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule]));

        self::assertSame(
            $resolved ? [$rule] : [],
            $this->layoutResolver->resolveRules($this->requestStack->getCurrentRequest()),
        );
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRule
     */
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
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule3 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule4 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->willReturnMap(
                [
                    [$ruleGroup, 'target1', 42, new RuleList([$rule1, $rule2])],
                    [$ruleGroup, 'target2', 84, new RuleList([$rule3, $rule4])],
                ],
            );

        self::assertSame($rule3, $this->layoutResolver->resolveRule($this->requestStack->getCurrentRequest()));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithInvalidRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame($rule1, $this->layoutResolver->resolveRule($this->requestStack->getCurrentRequest()));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoValidRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertNull($this->layoutResolver->resolveRule($this->requestStack->getCurrentRequest()));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRule
     */
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
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'priority' => 7,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target2'), self::identicalTo(84))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame($rule2, $this->layoutResolver->resolveRule($this->requestStack->getCurrentRequest()));
    }

    /**
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRule
     */
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

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('matchRules');

        self::assertNull($this->layoutResolver->resolveRule($this->requestStack->getCurrentRequest()));
    }

    /**
     * @param string[] $conditionTypes
     *
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::conditionsMatch
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRule
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsDataProvider
     */
    public function testResolveRuleWithConditionsAndPartialConditionMatching(array $conditionTypes, bool $resolved): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::uuid4()]) : null,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule]));

        self::assertSame(
            $resolved ? $rule : null,
            $this->layoutResolver->resolveRule(
                $this->requestStack->getCurrentRequest(),
                ['condition2'],
            ),
        );
    }

    /**
     * @param string[] $conditionTypes
     *
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::conditionsMatch
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveGroupRules
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::resolveRule
     *
     * @dataProvider resolveRulesWithRuleConditionsDataProvider
     */
    public function testResolveRuleWithConditions(array $conditionTypes, bool $resolved): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => $resolved ? Layout::fromArray(['id' => Uuid::uuid4()]) : null,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ],
        );

        $ruleGroup = new RuleGroup();

        $this->layoutResolverServiceMock
            ->method('loadRuleGroup')
            ->willReturn($ruleGroup);

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('matchRules')
            ->with(self::identicalTo($ruleGroup), self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule]));

        self::assertSame($resolved ? $rule : null, $this->layoutResolver->resolveRule($this->requestStack->getCurrentRequest()));
    }

    /**
     * @param string[] $conditionTypes
     *
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::conditionsMatch
     * @covers \Netgen\Layouts\Layout\Resolver\LayoutResolver::matches
     *
     * @dataProvider matchesDataProvider
     */
    public function testMatches(array $conditionTypes, bool $isMatch): void
    {
        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = RuleCondition::fromArray(['conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => Uuid::uuid4()]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ],
        );

        self::assertSame($isMatch, $this->layoutResolver->matches($rule, Request::create('/')));
    }

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

    public static function matchesDataProvider(): iterable
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

    private function createLayoutResolver(): void
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            $this->targetTypeRegistry,
            $this->requestStack,
        );
    }
}
