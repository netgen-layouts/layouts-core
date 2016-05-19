<?php

namespace Netgen\BlockManager\Tests;

use Netgen\BlockManager\Tests\Stubs\Value;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Value::__construct
     */
    public function testSetProperties()
    {
        $value = new Value(
            array(
                'someProperty' => 42,
                'someOtherProperty' => 84,
            )
        );

        self::assertEquals(42, $value->someProperty);
        self::assertEquals(84, $value->someOtherProperty);
    }

    /**
     * @covers \Netgen\BlockManager\Value::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testSetNonExistingProperties()
    {
        $value = new Value(
            array(
                'someNonExistingProperty' => 42,
            )
        );
    }
}
