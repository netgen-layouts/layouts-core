<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Netgen\BlockManager\Layout\Resolver\Condition;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Condition::__construct
     */
    public function testConstructor()
    {
        $condition = new Condition('identifier', array('value'));
        self::assertEquals('identifier', $condition->identifier);
        self::assertEquals(array('value'), $condition->parameters);
    }
}
