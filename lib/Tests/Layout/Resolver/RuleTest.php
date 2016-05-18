<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Netgen\BlockManager\Layout\Resolver\Rule;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\Target;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Rule::__construct
     */
    public function testConstructor()
    {
        $target = new Target(array('values'));
        $rule = new Rule(42, $target, array('conditions'));
        self::assertEquals(42, $rule->layoutId);
        self::assertEquals($target, $rule->target);
        self::assertEquals(array('conditions'), $rule->conditions);
    }
}
