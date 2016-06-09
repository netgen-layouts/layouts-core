<?php

namespace Netgen\BlockManager\Tests;

use Netgen\BlockManager\Tests\Stubs\ValueObject;

class ValueObjectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Netgen\BlockManager\ValueObject::__construct
     */
    public function testSetProperties()
    {
        $value = new ValueObject(
            array(
                'someProperty' => 42,
                'someOtherProperty' => 84,
            )
        );

        self::assertEquals(42, $value->someProperty);
        self::assertEquals(84, $value->someOtherProperty);
    }

    /**
     * @covers \Netgen\BlockManager\ValueObject::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testSetNonExistingProperties()
    {
        $value = new ValueObject(
            array(
                'someNonExistingProperty' => 42,
            )
        );
    }
}
