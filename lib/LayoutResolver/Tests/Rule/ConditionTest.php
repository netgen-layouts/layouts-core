<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Rule;

use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Condition;
use PHPUnit_Framework_TestCase;

class ConditionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::getWhat
     */
    public function testGetDefaultWhat()
    {
        $condition = new Condition();
        self::assertNull($condition->getWhat());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::setWhat
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::getWhat
     */
    public function testGetWhat()
    {
        $condition = new Condition();
        $condition->setWhat('what');

        self::assertEquals('what', $condition->getWhat());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::getValues
     */
    public function testGetDefaultValues()
    {
        $condition = new Condition();
        self::assertNull($condition->getValues());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::setValues
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::getValues
     */
    public function testGetValues()
    {
        $condition = new Condition();
        $condition->setValues(array(42));

        self::assertEquals(array(42), $condition->getValues());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::setValues
     * @expectedException \InvalidArgumentException
     */
    public function testSetValuesThrowsInvalidArgumentException()
    {
        $condition = new Condition();
        $condition->setValues(array());

        $values = $condition->getValues();
    }
}
