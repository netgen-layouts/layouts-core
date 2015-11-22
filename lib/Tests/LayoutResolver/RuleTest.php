<?php

namespace Netgen\BlockManager\Tests\LayoutResolver;

use Netgen\BlockManager\LayoutResolver\Rule;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule::__construct
     */
    public function testConstructor()
    {
        $rule = new Rule(42, array('conditions'));
        self::assertEquals(42, $rule->layoutId);
        self::assertEquals(array('conditions'), $rule->conditions);
    }
}
