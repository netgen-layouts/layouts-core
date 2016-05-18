<?php

namespace Netgen\BlockManager\Tests\Layout\Resolver;

use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\Target;

class TargetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target::__construct
     * @covers \Netgen\BlockManager\Layout\Resolver\Target::getValues
     */
    public function testConstructor()
    {
        $target = new Target(array('value'));
        self::assertEquals(array('value'), $target->getValues());
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Resolver\Target::setValues
     * @covers \Netgen\BlockManager\Layout\Resolver\Target::getValues
     */
    public function testSetValues()
    {
        $target = new Target();
        $target->setValues(array('value'));
        self::assertEquals(array('value'), $target->getValues());
    }
}
