<?php

namespace Netgen\BlockManager\LayoutResolver\Tests;

use Netgen\BlockManager\LayoutResolver\Rule;
use Netgen\BlockManager\LayoutResolver\LayoutResolver;
use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Condition;
use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Target;
use PHPUnit_Framework_TestCase;

class LayoutResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveLayout()
    {
        $rule = new Rule(42, new Target());

        $layoutResolver = new LayoutResolver(array($rule));
        self::assertEquals(42, $layoutResolver->resolveLayout());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveLayoutWithNoMatchingRules()
    {
        $rule1 = new Rule(42, new Target(false));
        $rule2 = new Rule(84, new Target(false));

        $layoutResolver = new LayoutResolver(array($rule1, $rule2));
        self::assertNull($layoutResolver->resolveLayout());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveLayoutWithAnyMatchingRule()
    {
        $rule1 = new Rule(24, new Target(false));
        $rule2 = new Rule(42, new Target(true));
        $rule3 = new Rule(84, new Target(false));

        $layoutResolver = new LayoutResolver(array($rule1, $rule2, $rule3));
        self::assertEquals(42, $layoutResolver->resolveLayout());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveFirstLayoutWithMoreThanOneMatchingRule()
    {
        $rule1 = new Rule(42, new Target());
        $rule2 = new Rule(24, new Target());

        $layoutResolver = new LayoutResolver(array($rule1, $rule2));
        self::assertEquals(42, $layoutResolver->resolveLayout());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::matchConditions
     *
     * @param array $conditions
     * @param int $layoutId
     *
     * @dataProvider resolveLayoutWithRuleConditionsProvider
     */
    public function testResolveLayoutWithRuleConditions(array $conditions, $layoutId)
    {
        $rule = new Rule(42, new Target(), $conditions);

        $layoutResolver = new LayoutResolver(array($rule));
        self::assertEquals($layoutId, $layoutResolver->resolveLayout());
    }

    /**
     * Data provider for {@link self::testResolveLayoutWithRuleConditions}.
     *
     * @return array
     */
    public function resolveLayoutWithRuleConditionsProvider()
    {
        return array(
            array(array(new Condition(true)), 42),
            array(array(new Condition(false)), null),
            array(array(new Condition(true), new Condition(false)), null),
            array(array(new Condition(false), new Condition(true)), null),
            array(array(new Condition(false), new Condition(false)), null),
            array(array(new Condition(true), new Condition(true)), 42),
        );
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveLayoutWithNoRules()
    {
        $layoutResolver = new LayoutResolver();
        self::assertNull($layoutResolver->resolveLayout());
    }
}
