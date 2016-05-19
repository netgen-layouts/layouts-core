<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\Tests\Core\Stubs\Value;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\AbstractValue::__construct
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
     * @covers \Netgen\BlockManager\API\Values\AbstractValue::__construct
     * @expectedException \Netgen\BlockManager\API\Exception\InvalidArgumentException
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
