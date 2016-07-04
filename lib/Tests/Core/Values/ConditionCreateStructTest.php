<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use PHPUnit\Framework\TestCase;

class ConditionCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $conditionCreateStruct = new ConditionCreateStruct();

        self::assertNull($conditionCreateStruct->type);
        self::assertNull($conditionCreateStruct->value);
    }

    public function testSetProperties()
    {
        $conditionCreateStruct = new ConditionCreateStruct(
            array(
                'type' => 'condition',
                'value' => 42,
            )
        );

        self::assertEquals('condition', $conditionCreateStruct->type);
        self::assertEquals(42, $conditionCreateStruct->value);
    }
}
