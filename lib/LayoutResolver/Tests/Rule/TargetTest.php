<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Rule;

use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Condition;
use Netgen\BlockManager\LayoutResolver\Tests\Stubs\Target;
use PHPUnit_Framework_TestCase;

class TargetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::getValues
     */
    public function testGetDefaultValues()
    {
        $target = new Target();
        self::assertNull($target->getValues());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::setValues
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::getValues
     */
    public function testGetValues()
    {
        $target = new Target();
        $target->setValues(array(42));

        self::assertEquals(array(42), $target->getValues());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::getConditions
     */
    public function testGetDefaultConditions()
    {
        $target = new Target();
        self::assertNull($target->getConditions());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::setConditions
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::getConditions
     */
    public function testGetConditions()
    {
        $target = new Target();

        $condition = new Condition();
        $target->setConditions(array($condition));

        self::assertEquals(array($condition), $target->getConditions());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::matches
     */
    public function testMatchesWhenTargetDoesNotEvaluate()
    {
        $target = new Target(false);

        self::assertEquals(false, $target->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::matches
     */
    public function testMatchesWithEmptyConditions()
    {
        $target = new Target();

        self::assertEquals(true, $target->matches());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Rule\Target::matches
     *
     * @param \Netgen\BlockManager\LayoutResolver\Rule\ConditionInterface[] $conditions
     * @param bool $matches
     *
     * @dataProvider matchesWithConditionsProvider
     */
    public function testMatchesWithConditions(array $conditions, $matches)
    {
        $target = new Target();
        $target->setConditions($conditions);

        self::assertEquals($matches, $target->matches());
    }

    /**
     * Data provider for {@link self::testMatchesWithConditions}.
     *
     * @return array
     */
    public function matchesWithConditionsProvider()
    {
        return array(
            array(array(new Condition(true, true)), true),
            array(array(new Condition(false, false)), false),
            array(array(new Condition(true, false)), false),
            array(array(new Condition(false, true)), false),
            array(array(new Condition(true, true), new Condition(true, true)), true),
            array(array(new Condition(false, false), new Condition(true, true)), true),
            array(array(new Condition(true, false), new Condition(true, true)), true),
            array(array(new Condition(false, true), new Condition(true, true)), false),
            array(array(new Condition(true, true), new Condition(false, false)), true),
            array(array(new Condition(false, false), new Condition(false, false)), false),
            array(array(new Condition(true, false), new Condition(false, false)), false),
            array(array(new Condition(false, true), new Condition(false, false)), false),
            array(array(new Condition(true, true), new Condition(true, false)), true),
            array(array(new Condition(false, false), new Condition(true, false)), false),
            array(array(new Condition(true, false), new Condition(true, false)), false),
            array(array(new Condition(false, true), new Condition(true, false)), false),
            array(array(new Condition(true, true), new Condition(false, true)), false),
            array(array(new Condition(false, false), new Condition(false, true)), false),
            array(array(new Condition(true, false), new Condition(false, true)), false),
            array(array(new Condition(false, true), new Condition(false, true)), false),
        );
    }
}
