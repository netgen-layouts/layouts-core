<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Netgen\BlockManager\Layout\Resolver\Condition;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Condition::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Condition::getIdentifier
     * @covers \Netgen\BlockManager\Layout\Resolver\Condition::getParameters
     */
    public function testConstructor()
    {
        $condition = new Condition('identifier', array('value'));
        self::assertEquals('identifier', $condition->getIdentifier());
        self::assertEquals(array('value'), $condition->getParameters());
    }
}
