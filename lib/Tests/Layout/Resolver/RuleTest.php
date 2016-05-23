<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Netgen\BlockManager\Layout\Resolver\Rule;
use Netgen\BlockManager\Layout\Resolver\Target;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Rule::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Rule::getLayoutId
     * @covers \Netgen\BlockManager\Layout\Resolver\Rule::getTarget
     * @covers \Netgen\BlockManager\Layout\Resolver\Rule::getConditions
     */
    public function testConstructor()
    {
        $target = new Target();
        $rule = new Rule(
            array(
                'layoutId' => 42,
                'target' => $target,
                'conditions' => array('conditions'),
            )
        );

        self::assertEquals(42, $rule->getLayoutId());
        self::assertEquals($target, $rule->getTarget());
        self::assertEquals(array('conditions'), $rule->getConditions());
    }
}
