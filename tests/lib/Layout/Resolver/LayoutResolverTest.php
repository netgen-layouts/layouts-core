<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

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
     * @var \PHPUnit\Framework\MockObject\MockObject
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

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);

        $this->targetTypeRegistry = new TargetTypeRegistry();

        $this->requestStack = new RequestStack();
        $this->requestStack->push(Request::create('/'));

        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            $this->targetTypeRegistry,
            $this->requestStack
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testResolveRules()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', 84));

        $rule1 = new Rule(
            array(
                'layout' => new Layout(array('id' => 12)),
                'priority' => 2,
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layout' => new Layout(array('id' => 13)),
                'priority' => 4,
                'enabled' => true,
            )
        );

        $rule3 = new Rule(
            array(
                'layout' => new Layout(array('id' => 14)),
                'priority' => 5,
                'enabled' => true,
            )
        );

        $rule4 = new Rule(
            array(
                'layout' => new Layout(array('id' => 15)),
                'priority' => 4,
                'enabled' => true,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue(array($rule1, $rule2)));

        $this->layoutResolverServiceMock
            ->expects($this->at(1))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue(array($rule3, $rule4)));

        $resolvedRules = $this->layoutResolver->resolveRules();

        $this->assertCount(4, $resolvedRules);

        // We can't be sure in what order two rules with same priority will be returned,
        // so just assert the first one and the last one
        $this->assertEquals($rule3, $resolvedRules[0]);
        $this->assertEquals($rule1, $resolvedRules[3]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testResolveRulesWithInvalidRule()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));

        $rule1 = new Rule(
            array(
                'layout' => new Layout(array('id' => 12)),
                'priority' => 2,
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue(array($rule1, $rule2)));

        $this->assertEquals(array($rule1), $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testResolveRulesWithNoValidRules()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', 42));

        $rule1 = new Rule(
            array(
                'layout' => null,
                'priority' => 2,
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layout' => null,
                'priority' => 4,
                'enabled' => true,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target1'), $this->equalTo(42))
            ->will($this->returnValue(array($rule1, $rule2)));

        $this->assertEquals(array(), $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testResolveRulesWithNoTargetValue()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', null));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', 84));

        $rule1 = new Rule(
            array(
                'layout' => new Layout(array('id' => 13)),
                'priority' => 5,
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layout' => new Layout(array('id' => 13)),
                'priority' => 7,
                'enabled' => true,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue(array($rule1, $rule2)));

        $this->assertEquals(array($rule2, $rule1), $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValues()
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target1', null));
        $this->targetTypeRegistry->addTargetType(new TargetType('target2', null));

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        $this->assertEquals(array(), $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     *
     * @param array $matches
     * @param int $layoutId
     *
     * @dataProvider resolveRulesWithPartialRuleConditionsProvider
     */
    public function testResolveRulesWithConditionsAndPartialConditionMatching(array $matches, $layoutId)
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $conditions = array();
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(array('conditionType' => new ConditionType($conditionType, $match)));
        }

        $rule = new Rule(
            array(
                'layout' => new Layout(array('id' => $layoutId)),
                'enabled' => true,
                'conditions' => $conditions,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue(array($rule)));

        $this->assertEquals(
            $layoutId !== null ? array($rule) : array(),
            $this->layoutResolver->resolveRules(null, array('condition2'))
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     *
     * @param array $matches
     * @param int $layoutId
     *
     * @dataProvider resolveRulesWithRuleConditionsProvider
     */
    public function testResolveRulesWithConditions(array $matches, $layoutId)
    {
        $this->targetTypeRegistry->addTargetType(new TargetType('target', 42));

        $conditions = array();
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(array('conditionType' => new ConditionType($conditionType, $match)));
        }

        $rule = new Rule(
            array(
                'layout' => new Layout(array('id' => $layoutId)),
                'enabled' => true,
                'conditions' => $conditions,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue(array($rule)));

        $this->assertEquals(
            $layoutId !== null ? array($rule) : array(),
            $this->layoutResolver->resolveRules()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testMatchRules()
    {
        $rule1 = new Rule(
            array(
                'layout' => new Layout(array('id' => 12)),
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layout' => new Layout(array('id' => 13)),
                'enabled' => true,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target'), $this->equalTo(42))
            ->will($this->returnValue(array($rule1, $rule2)));

        $this->assertEquals(array($rule1, $rule2), $this->layoutResolver->matchRules('target', 42));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testMatchRulesWithInvalidRules()
    {
        $rule1 = new Rule(
            array(
                'layout' => null,
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layout' => new Layout(array('id' => 13)),
                'enabled' => false,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target'), $this->equalTo(42))
            ->will($this->returnValue(array($rule1, $rule2)));

        $this->assertEquals(array(), $this->layoutResolver->matchRules('target', 42));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testMatchRulesWithNoRules()
    {
        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target'), $this->equalTo(42))
            ->will($this->returnValue(array()));

        $this->assertEquals(array(), $this->layoutResolver->matchRules('target', 42));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matches
     *
     * @param array $matches
     * @param bool $isMatch
     *
     * @dataProvider matchesProvider
     */
    public function testMatches(array $matches, $isMatch)
    {
        $conditions = array();
        foreach ($matches as $conditionType => $match) {
            $conditions[] = new Condition(array('conditionType' => new ConditionType($conditionType, $match)));
        }

        $rule = new Rule(
            array(
                'layout' => new Layout(array('id' => 42)),
                'enabled' => true,
                'conditions' => $conditions,
            )
        );

        $this->assertEquals($isMatch, $this->layoutResolver->matches($rule, Request::create('/')));
    }

    public function resolveRulesWithRuleConditionsProvider()
    {
        return array(
            array(array(), 42),
            array(array('condition' => true), 42),
            array(array('condition' => false), null),
            array(array('condition1' => true, 'condition2' => false), null),
            array(array('condition1' => false, 'condition2' => true), null),
            array(array('condition1' => false, 'condition2' => false), null),
            array(array('condition1' => true, 'condition2' => true), 42),
        );
    }

    public function resolveRulesWithPartialRuleConditionsProvider()
    {
        return array(
            array(array(), 42),
            array(array('condition' => true), 42),
            array(array('condition' => false), 42),
            array(array('condition1' => true, 'condition2' => false), null),
            array(array('condition1' => false, 'condition2' => true), 42),
            array(array('condition1' => false, 'condition2' => false), null),
            array(array('condition1' => true, 'condition2' => true), 42),
        );
    }

    public function matchesProvider()
    {
        return array(
            array(array(), true),
            array(array('condition' => true), true),
            array(array('condition' => false), false),
            array(array('condition1' => true, 'condition2' => false), false),
            array(array('condition1' => false, 'condition2' => true), false),
            array(array('condition1' => false, 'condition2' => false), false),
            array(array('condition1' => true, 'condition2' => true), true),
        );
    }
}
