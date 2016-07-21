<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use PHPUnit\Framework\TestCase;

class ConditionUpdateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $conditionUpdateStruct = new ConditionUpdateStruct();

        $this->assertNull($conditionUpdateStruct->value);
    }

    public function testSetProperties()
    {
        $conditionUpdateStruct = new ConditionUpdateStruct(
            array(
                'value' => 42,
            )
        );

        $this->assertEquals(42, $conditionUpdateStruct->value);
    }
}
