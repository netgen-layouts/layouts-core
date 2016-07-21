<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use PHPUnit\Framework\TestCase;

class ConditionCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $conditionCreateStruct = new ConditionCreateStruct();

        $this->assertNull($conditionCreateStruct->type);
        $this->assertNull($conditionCreateStruct->value);
    }

    public function testSetProperties()
    {
        $conditionCreateStruct = new ConditionCreateStruct(
            array(
                'type' => 'condition',
                'value' => 42,
            )
        );

        $this->assertEquals('condition', $conditionCreateStruct->type);
        $this->assertEquals(42, $conditionCreateStruct->value);
    }
}
