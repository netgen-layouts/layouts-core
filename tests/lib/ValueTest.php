<?php

namespace Netgen\BlockManager\Tests;

use Netgen\BlockManager\Tests\Stubs\Value;
use PHPUnit\Framework\TestCase;

final class ValueTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Value::__construct
     */
    public function testSetProperties()
    {
        $value = new Value(
            [
                'someProperty' => 42,
                'someOtherProperty' => 84,
            ]
        );

        $this->assertEquals(42, $value->someProperty);
        $this->assertEquals(84, $value->someOtherProperty);
    }

    /**
     * @covers \Netgen\BlockManager\Value::__construct
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Property "someNonExistingProperty" does not exist in "Netgen\BlockManager\Tests\Stubs\Value" class.
     */
    public function testSetNonExistingProperties()
    {
        new Value(
            [
                'someNonExistingProperty' => 42,
            ]
        );
    }
}
