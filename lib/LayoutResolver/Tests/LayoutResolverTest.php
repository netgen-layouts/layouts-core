<?php

namespace Netgen\BlockManager\LayoutResolver\Tests;

use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Rule;
use Netgen\BlockManager\LayoutResolver\LayoutResolver;
use Netgen\BlockManager\Core\Values\Page\Layout;
use PHPUnit_Framework_TestCase;

class LayoutResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveLayout()
    {
        $rule = new Rule(42);

        $layoutResolver = new LayoutResolver(array($rule));
        self::assertEquals(42, $layoutResolver->resolveLayout());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveLayoutWithNonMatchingRules()
    {
        $rule = new Rule(42, false);

        $layoutResolver = new LayoutResolver(array($rule));
        self::assertNull($layoutResolver->resolveLayout());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveFirstLayoutWithMoreThanOneMatchingRule()
    {
        $rule1 = new Rule(42);
        $rule2 = new Rule(24);

        $layoutResolver = new LayoutResolver(array($rule1, $rule2));
        self::assertEquals(42, $layoutResolver->resolveLayout());
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
