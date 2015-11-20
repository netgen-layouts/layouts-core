<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Rule;

use Netgen\BlockManager\LayoutResolver\Rule\Rule;
use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Target;
use PHPUnit_Framework_TestCase;

class RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Rule::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Rule::getLayoutId
     */
    public function testGetLayoutId()
    {
        $rule = new Rule(42, array(new Target()));

        self::assertEquals(42, $rule->getLayoutId());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Rule::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Rule::getTargets
     */
    public function testGetTargets()
    {
        $target = new Target();
        $rule = new Rule(42, array($target));

        self::assertEquals(array($target), $rule->getTargets());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Rule::matches
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule\TargetInterface[] $targets
     * @param bool $matches
     *
     * @dataProvider matchesProvider
     */
    public function testMatches(array $targets, $matches)
    {
        $rule = new Rule(42, $targets);
        self::assertEquals($matches, $rule->matches());
    }

    /**
     * Data provider for {@link self::testMatches}.
     *
     * @return array
     */
    public function matchesProvider()
    {
        return array(
            array(array(), false),
            array(array(new Target(true)), true),
            array(array(new Target(false)), false),
            array(array(new Target(false), new Target(true)), true),
            array(array(new Target(true), new Target(false)), true),
            array(array(new Target(true), new Target(true)), true),
            array(array(new Target(false), new Target(false)), false),
        );
    }
}
