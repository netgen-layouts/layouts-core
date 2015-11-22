<?php

namespace Netgen\BlockManager\LayoutResolver\Tests;

use Netgen\BlockManager\LayoutResolver\Target;

class TargetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\LayoutResolver\Target::__construct
     */
    public function testConstructor()
    {
        $target = new Target('identifier', array('value'));
        self::assertEquals('identifier', $target->identifier);
        self::assertEquals(array('value'), $target->values);
    }
}
