<?php

namespace Netgen\BlockManager\Persistence\Tests\Values;

use Netgen\BlockManager\Persistence\Tests\Stubs\Value;
use PHPUnit_Framework_TestCase;

class ValueTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Persistence\Values\Value::__construct
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
     * @covers \Netgen\BlockManager\Persistence\Values\Value::__construct
     * @expectedException \Netgen\BlockManager\Exceptions\InvalidArgumentException
     */
    public function testSetNonExistingProperties()
    {
        $value = new Value(
            array(
                'someNonExistingProperty',
            )
        );
    }
}
