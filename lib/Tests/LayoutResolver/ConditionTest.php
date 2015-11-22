<?php

namespace Netgen\BlockManager\Tests\LayoutResolver;

use Netgen\BlockManager\LayoutResolver\Condition;

class ConditionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Condition::__construct
     */
    public function testConstructor()
    {
        $condition = new Condition('identifier', 'value_identifier', array('value'));
        self::assertEquals('identifier', $condition->identifier);
        self::assertEquals('value_identifier', $condition->valueIdentifier);
        self::assertEquals(array('value'), $condition->values);
    }
}
