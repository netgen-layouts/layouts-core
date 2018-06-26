<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\LayoutResolver;
use Netgen\BlockManager\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetType;
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

        $this->targetTypeRegistry = new TargetTypeRegistry();

        $this->requestStack = new RequestStack();
        $this->requestStack->push(Request::create('/'));

        $this->createLayoutResolver();
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target1', 42),
            new TargetType('target2', 84)
        );

        $this->createLayoutResolver();

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule3 = new Rule(
            [
                'layout' => new Layout(['id' => 14]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule4 = new Rule(
            [
                'layout' => new Layout(['id' => 15]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->layoutResolverServiceMock
            ->expects($this->at(1))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue([$rule3, $rule4]));

        $resolvedRules = $this->layoutResolver->resolveRules();

        $this->assertCount(4, $resolvedRules);

        // We can't be sure in what order two rules with same priority will be returned,
        // so just assert the first one and the last one
        $this->assertSame($rule3, $resolvedRules[0]);
        $this->assertSame($rule1, $resolvedRules[3]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithInvalidRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(new TargetType('target1', 42));

        $this->createLayoutResolver();

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertSame([$rule1], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoValidRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(new TargetType('target1', 42));

        $this->createLayoutResolver();

        $rule1 = new Rule(
            [
                'layout' => null,
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertSame([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValue(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target1', null),
            new TargetType('target2', 84)
        );

        $this->createLayoutResolver();

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 7,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertSame([$rule2, $rule1], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValues(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target1', null),
            new TargetType('target2', null)
        );

        $this->createLayoutResolver();

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertSame([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoRequest(): void
    {
        $this->requestStack->pop();

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertSame([], $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsProvider
     */
    public function testResolveRulesWithConditionsAndPartialConditionMatching(array $matches, ?int $layoutId): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(new TargetType('target', 42));

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue([$rule]));

        $this->assertSame(
            $layoutId !== null ? [$rule] : [],
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
    public function testResolveRulesWithConditions(array $matches, ?int $layoutId): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(new TargetType('target', 42));

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue([$rule]));

        $this->assertSame(
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
            new TargetType('target1', 42),
            new TargetType('target2', 84)
        );

        $this->createLayoutResolver();

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule3 = new Rule(
            [
                'layout' => new Layout(['id' => 14]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule4 = new Rule(
            [
                'layout' => new Layout(['id' => 15]),
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->layoutResolverServiceMock
            ->expects($this->at(1))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue([$rule3, $rule4]));

        $this->assertSame($rule3, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithInvalidRule(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target1', 42)
        );

        $this->createLayoutResolver();

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 12]),
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertSame($rule1, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoValidRules(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target1', 42)
        );

        $this->createLayoutResolver();

        $rule1 = new Rule(
            [
                'layout' => null,
                'priority' => 2,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoTargetValue(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target1', null),
            new TargetType('target2', 84)
        );

        $this->createLayoutResolver();

        $rule1 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 5,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $rule2 = new Rule(
            [
                'layout' => new Layout(['id' => 13]),
                'priority' => 7,
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection(),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue([$rule1, $rule2]));

        $this->assertSame($rule2, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoTargetValues(): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target1', null),
            new TargetType('target2', null)
        );

        $this->createLayoutResolver();

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     */
    public function testResolveRuleWithNoRequest(): void
    {
        $this->requestStack->pop();

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertNull($this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsProvider
     */
    public function testResolveRuleWithConditionsAndPartialConditionMatching(array $matches, ?int $layoutId): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target', 42)
        );

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue([$rule]));

        $this->assertSame($layoutId !== null ? $rule : null, $this->layoutResolver->resolveRule(null, ['condition2']));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::innerResolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRule
     *
     * @dataProvider resolveRulesWithRuleConditionsProvider
     */
    public function testResolveRuleWithConditions(array $matches, ?int $layoutId): void
    {
        $this->targetTypeRegistry = new TargetTypeRegistry(
            new TargetType('target', 42)
        );

        $this->createLayoutResolver();

        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => $layoutId]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue([$rule]));

        $this->assertSame($layoutId !== null ? $rule : null, $this->layoutResolver->resolveRule());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches(array $matches, bool $isMatch): void
    {
        $conditions = [];
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(['conditionType' => new ConditionType($conditionType, $match)]);
        }

        $rule = new Rule(
            [
                'layout' => new Layout(['id' => 42]),
                'enabled' => true,
                'targets' => new ArrayCollection(),
                'conditions' => new ArrayCollection($conditions),
            ]
        );

        $this->assertSame($isMatch, $this->layoutResolver->matches($rule, Request::create('/')));
    }

    public function resolveRulesWithRuleConditionsProvider(): array
    {
        return [
            [[], 42],
            [['condition' => true], 42],
            [['condition' => false], null],
            [['condition1' => true, 'condition2' => false], null],
            [['condition1' => false, 'condition2' => true], null],
            [['condition1' => false, 'condition2' => false], null],
            [['condition1' => true, 'condition2' => true], 42],
        ];
    }

    public function resolveRulesWithPartialRuleConditionsProvider(): array
    {
        return [
            [[], 42],
            [['condition' => true], 42],
            [['condition' => false], 42],
            [['condition1' => true, 'condition2' => false], null],
            [['condition1' => false, 'condition2' => true], 42],
            [['condition1' => false, 'condition2' => false], null],
            [['condition1' => true, 'condition2' => true], 42],
        ];
    }

    public function matchesProvider(): array
    {
        return [
            [[], true],
            [['condition' => true], true],
            [['condition' => false], false],
            [['condition1' => true, 'condition2' => false], false],
            [['condition1' => false, 'condition2' => true], false],
            [['condition1' => false, 'condition2' => false], false],
            [['condition1' => true, 'condition2' => true], true],
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
