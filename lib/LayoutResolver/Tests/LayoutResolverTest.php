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
        $layout = new Layout(array('identifier' => '3_zones_a'));
        $rule = new Rule($layout);

        $layoutResolver = new LayoutResolver(array($rule));
        self::assertEquals($layout, $layoutResolver->resolveLayout());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveLayoutWithNonMatchingRules()
    {
        $layout = new Layout(array('identifier' => '3_zones_a'));
        $rule = new Rule($layout, false);

        $layoutResolver = new LayoutResolver(array($rule));
        self::assertNull($layoutResolver->resolveLayout());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\LayoutResolver::resolveLayout
     */
    public function testResolveFirstLayoutWithMoreThanOneMatchingRule()
    {
        $layout1 = new Layout(array('identifier' => '3_zones_a'));
        $layout2 = new Layout(array('identifier' => '3_zones_b'));

        $rule1 = new Rule($layout1);
        $rule2 = new Rule($layout2);

        $layoutResolver = new LayoutResolver(array($rule1, $rule2));
        self::assertEquals($layout1, $layoutResolver->resolveLayout());
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
