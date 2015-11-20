<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Rule;

use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Condition;
use PHPUnit_Framework_TestCase;

class ConditionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::getIdentifier
     */
    public function testGetDefaultIdentifier()
    {
        $condition = new Condition();
        self::assertNull($condition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::setIdentifier
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Condition::getIdentifier
     */
    public function testGetIdentifier()
    {
        $condition = new Condition();
        $condition->setIdentifier('identifier');

        self::assertEquals('identifier', $condition->getIdentifier());
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
