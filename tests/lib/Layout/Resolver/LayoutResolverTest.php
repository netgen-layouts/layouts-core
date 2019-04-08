<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleList;
use Netgen\BlockManager\Layout\Resolver\LayoutResolver;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType1;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType2;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType3;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType1;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType2;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class LayoutResolverTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject&\Netgen\BlockManager\API\Service\LayoutResolverService
     */
    private $layoutResolverServiceMock;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistryInterface
     */
    private $targetTypeRegistry;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface
     */
    private $layoutResolver;

    public function setUp(): void
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->requestStack = new RequestStack();
        $this->requestStack->push(Request::create('/'));

        $this->targetTypeRegistry = new TargetTypeRegistry([]);
        $this->createLayoutResolver();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42), new TargetType2(84)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 13]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule3 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 14]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule4 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 15]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        $this->layoutResolverServiceMock
            ->expects(self::at(1))
            ->method('matchRules')
            ->with(self::identicalTo('target2'), self::identicalTo(84))
            ->willReturn(new RuleList([$rule3, $rule4]));

        $resolvedRules = $this->layoutResolver->resolveRules();

        self::assertCount(4, $resolvedRules);

        // We can't be sure in what order two rules with same priority will be returned,
        // so just assert the first one and the last one
        self::assertSame($rule3, $resolvedRules[0]);
        self::assertSame($rule1, $resolvedRules[3]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithInvalidRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame([$rule1], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithDisabledRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 24]),
                'priority' => 4,
                'enabled' => false,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame([$rule1], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
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
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValue(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(null),
                new TargetType2(84),
            ]
        );

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 13]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 13]),
                'priority' => 7,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target2'), self::identicalTo(84))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame([$rule2, $rule1], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValues(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(null),
                new TargetType2(null),
            ]
        );

        $this->createLayoutResolver();

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('matchRules');

        self::assertSame([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoRequest(): void
    {
        $this->requestStack->pop();

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('matchRules');

        self::assertSame([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsProvider
     */
    public function testResolveRulesWithConditionsAndPartialConditionMatching(array $conditionTypes, ?int $layoutId): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = Condition::fromArray(['conditionType' => $conditionType]);
        }

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'priority' => 4,
                'conditions' => new ArrayCollection([Condition::fromArray(['conditionType' => new ConditionType2(false)])]),
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'priority' => 2,
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame(
            $layoutId !== null ? [$rule2] : [],
            $this->layoutResolver->resolveRules(null, ['condition2'])
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     *
     * @dataProvider resolveRulesWithRuleConditionsProvider
     */
    public function testResolveRulesWithConditions(array $conditionTypes, ?int $layoutId): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = Condition::fromArray(['conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule]));

        self::assertSame(
            $layoutId !== null ? [$rule] : [],
            $this->layoutResolver->resolveRules()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(42),
                new TargetType2(84),
            ]
        );

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 13]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule3 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 14]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule4 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 15]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        $this->layoutResolverServiceMock
            ->expects(self::at(1))
            ->method('matchRules')
            ->with(self::identicalTo('target2'), self::identicalTo(84))
            ->willReturn(new RuleList([$rule3, $rule4]));

        self::assertSame($rule3, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithInvalidRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame($rule1, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
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
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoTargetValue(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(null),
                new TargetType2(84),
            ]
        );

        $this->createLayoutResolver();

        $rule1 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 13]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 13]),
                'priority' => 7,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::at(0))
            ->method('matchRules')
            ->with(self::identicalTo('target2'), self::identicalTo(84))
            ->willReturn(new RuleList([$rule1, $rule2]));

        self::assertSame($rule2, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoTargetValues(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            [
                new TargetType1(null),
                new TargetType2(null),
            ]
        );

        $this->createLayoutResolver();

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('matchRules');

        self::assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoRequest(): void
    {
        $this->requestStack->pop();

        $this->layoutResolverServiceMock
            ->expects(self::never())
            ->method('matchRules');

        self::assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsProvider
     */
    public function testResolveRuleWithConditionsAndPartialConditionMatching(array $conditionTypes, ?int $layoutId): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = Condition::fromArray(['conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule]));

        self::assertSame($layoutId !== null ? $rule : null, $this->layoutResolver->resolveRule(null, ['condition2']));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     *
     * @dataProvider resolveRulesWithRuleConditionsProvider
     */
    public function testResolveRuleWithConditions(array $conditionTypes, ?int $layoutId): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry([new TargetType1(42)]);

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = Condition::fromArray(['conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects(self::once())
            ->method('matchRules')
            ->with(self::identicalTo('target1'), self::identicalTo(42))
            ->willReturn(new RuleList([$rule]));

        self::assertSame($layoutId !== null ? $rule : null, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches(array $conditionTypes, bool $isMatch): void
    {
        $conditions = [];
        foreach ($conditionTypes as $conditionType) {
            $conditions[] = Condition::fromArray(['conditionType' => $conditionType]);
        }

        $rule = Rule::fromArray(
            [
                'layout' => Layout::fromArray(['id' => 42]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        self::assertSame($isMatch, $this->layoutResolver->matches($rule, Request::create('/')));
    }

    public function resolveRulesWithRuleConditionsProvider(): array
    {
        return [
            [[], 42],
            [[new ConditionType3(true)], 42],
            [[new ConditionType3(false)], null],
            [[new ConditionType1(true), new ConditionType2(false)], null],
            [[new ConditionType1(false), new ConditionType2(true)], null],
            [[new ConditionType1(false), new ConditionType2(false)], null],
            [[new ConditionType1(true), new ConditionType2(true)], 42],
        ];
    }

    public function resolveRulesWithPartialRuleConditionsProvider(): array
    {
        return [
            [[], 42],
            [[new ConditionType3(true)], 42],
            [[new ConditionType3(false)], 42],
            [[new ConditionType1(true), new ConditionType2(false)], null],
            [[new ConditionType1(false), new ConditionType2(true)], 42],
            [[new ConditionType1(false), new ConditionType2(false)], null],
            [[new ConditionType1(true), new ConditionType2(true)], 42],
        ];
    }

    public function matchesProvider(): array
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
            $this->requestStack
        );
    }
}
