<?php

namespace Netgen\BlockManager\LayoutResolver\Tests\Rule;

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
}
