<?php

namespace Netgen\BlockManager\Tests\LayoutResolver;

use Netgen\BlockManager\LayoutResolver\Rule;
use Netgen\BlockManager\LayoutResolver\Target;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule::__construct
     */
    public function testConstructor()
    {
        $target = new Target('target', array('values'));
        $rule = new Rule(42, $target, array('conditions'));
        self::assertEquals(42, $rule->layoutId);
        self::assertEquals($target, $rule->target);
        self::assertEquals(array('conditions'), $rule->conditions);
    }
}
