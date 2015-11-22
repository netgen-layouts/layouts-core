<?php

namespace Netgen\BlockManager\Tests\LayoutResolver;

use Netgen\BlockManager\LayoutResolver\Condition;
use Netgen\BlockManager\Tests\LayoutResolver\Stubs\ConditionMatcher;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Condition::__construct
     */
    public function testConstructor()
    {
        $condition = new Condition(new ConditionMatcher(), 'identifier', array('value'));
        self::assertEquals(new ConditionMatcher(), $condition->conditionMatcher);
        self::assertEquals('identifier', $condition->valueIdentifier);
        self::assertEquals(array('value'), $condition->values);
    }
}
