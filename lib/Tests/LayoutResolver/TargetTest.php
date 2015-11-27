<?php

namespace Netgen\BlockManager\Tests\LayoutResolver;

use Netgen\BlockManager\Tests\LayoutResolver\Stubs\Target;

class TargetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Target::__construct
     * @covers \Netgen\BlockManager\LayoutResolver\Target::getValues
     */
    public function testConstructor()
    {
        $target = new Target(array('value'));
        self::assertEquals(array('value'), $target->getValues());
    }

    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Target::setValues
     * @covers \Netgen\BlockManager\LayoutResolver\Target::getValues
     */
    public function testSetValues()
    {
        $target = new Target();
        $target->setValues(array('value'));
        self::assertEquals(array('value'), $target->getValues());
    }
}
