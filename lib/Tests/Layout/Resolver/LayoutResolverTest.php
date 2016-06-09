<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Netgen\BlockManager\API\Service\LayoutResolverService;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\LayoutResolver;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionMatcher;
use Netgen\BlockManager\Layout\Resolver\Target;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\TargetValueProvider;
use stdClass;

class LayoutResolverTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $layoutResolverServiceMock;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface
     */
    protected $layoutResolver;

    public function setUp()
    {
        $this->layoutResolverServiceMock = $this->createMock(LayoutResolverService::class);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::__construct
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionOnWrongValueProviderInterface()
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(
                'target1' => new stdClass(null),
            ),
            array()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::__construct
     * @expectedException \RuntimeException
     */
    public function testConstructorThrowsRuntimeExceptionOnWrongMatcherInterface()
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(),
            array(
                'condition' => new stdClass(),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRules()
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(
                'target1' => new TargetValueProvider(42),
                'target2' => new TargetValueProvider(84),
            ),
            array()
        );

        $rule1 = new Rule(
            array(
                'layoutId' => 12,
                'priority' => 2,
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layoutId' => 13,
                'priority' => 4,
                'enabled' => true,
            )
        );

        $rule3 = new Rule(
            array(
                'layoutId' => 14,
                'priority' => 5,
                'enabled' => true,
            )
        );

        $rule4 = new Rule(
            array(
                'layoutId' => 15,
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

        self::assertCount(4, $resolvedRules);

        // We can't be sure in what order two rules with same priority will be returned,
        // so just assert the first one and the last one
        self::assertEquals($rule3, $resolvedRules[0]);
        self::assertEquals($rule1, $resolvedRules[3]);
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValue()
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(
                'target1' => new TargetValueProvider(null),
                'target2' => new TargetValueProvider(84),
            ),
            array()
        );

        $rule1 = new Rule(
            array(
                'layoutId' => 13,
                'priority' => 5,
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layoutId' => 13,
                'priority' => 7,
                'enabled' => true,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->at(0))
            ->method('matchRules')
            ->with($this->equalTo('target2'), $this->equalTo(84))
            ->will($this->returnValue(array($rule1, $rule2)));

        self::assertEquals(array($rule2, $rule1), $this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::resolveRules
     */
    public function testResolveRulesWithNoTargetValues()
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(
                'target1' => new TargetValueProvider(null),
                'target2' => new TargetValueProvider(null),
            ),
            array()
        );

        $this->layoutResolverServiceMock
            ->expects($this->never())
            ->method('matchRules');

        self::assertEmpty($this->layoutResolver->resolveRules());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testMatchRules()
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(),
            array()
        );

        $rule1 = new Rule(
            array(
                'layoutId' => 12,
                'enabled' => true,
            )
        );

        $rule2 = new Rule(
            array(
                'layoutId' => 13,
                'enabled' => true,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target'), $this->equalTo(42))
            ->will($this->returnValue(array($rule1, $rule2)));

        self::assertEquals(array($rule1, $rule2), $this->layoutResolver->matchRules('target', 42));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     */
    public function testMatchRulesWithNoRules()
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(),
            array()
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target'), $this->equalTo(42))
            ->will($this->returnValue(array()));

        self::assertEmpty($this->layoutResolver->matchRules('target', 42));
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchConditions
     *
     * @param array $matches
     * @param int $layoutId
     *
     * @dataProvider matchRulesWithRuleConditionsProvider
     */
    public function testMatchRulesWithConditions(array $matches, $layoutId)
    {
        $matchers = array();
        $conditions = array();
        foreach ($matches as $conditionIdentifier => $match) {
            $matchers[$conditionIdentifier] = new ConditionMatcher($match);
            $conditions[] = new Condition(array('identifier' => $conditionIdentifier));
        }

        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(),
            $matchers
        );

        $rule = new Rule(
            array(
                'layoutId' => $layoutId,
                'enabled' => true,
                'conditions' => $conditions,
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target', 42))
            ->will($this->returnValue(array($rule)));

        self::assertEquals(
            $layoutId !== null ? array($rule) : array(),
            $this->layoutResolver->matchRules('target', '42')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchRules
     * @covers \Netgen\BlockManager\Layout\Resolver\LayoutResolver::matchConditions
     * @expectedException \RuntimeException
     */
    public function testMatchRulesThrowsRuntimeExceptionOnNonExistingMatcher()
    {
        $this->layoutResolver = new LayoutResolver(
            $this->layoutResolverServiceMock,
            array(),
            array(
                'condition' => new ConditionMatcher(),
            )
        );

        $rule = new Rule(
            array(
                'layoutId' => 12,
                'enabled' => true,
                'conditions' => array(
                    new Condition(array('identifier' => 'unknown')),
                ),
            )
        );

        $this->layoutResolverServiceMock
            ->expects($this->once())
            ->method('matchRules')
            ->with($this->equalTo('target'), $this->equalTo(42))
            ->will($this->returnValue(array($rule)));

        $this->layoutResolver->matchRules('target', 42);
    }

    public function matchRulesWithRuleConditionsProvider()
    {
        return array(
            array(array('condition' => true), 42),
            array(array('condition' => false), null),
            array(array('condition1' => true, 'condition2' => false), null),
            array(array('condition1' => false, 'condition2' => true), null),
            array(array('condition1' => false, 'condition2' => false), null),
            array(array('condition1' => true, 'condition2' => true), 42),
        );
    }
}
