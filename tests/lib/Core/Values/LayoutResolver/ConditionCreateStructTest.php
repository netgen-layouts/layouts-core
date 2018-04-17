<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use PHPUnit\Framework\TestCase;

final class ConditionCreateStructTest extends TestCase
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
            [
                'type' => 'condition',
                'value' => 42,
            ]
        );

        $this->assertEquals('condition', $conditionCreateStruct->type);
        $this->assertEquals(42, $conditionCreateStruct->value);
    }
}
