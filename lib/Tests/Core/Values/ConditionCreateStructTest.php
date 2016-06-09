<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use PHPUnit\Framework\TestCase;

class ConditionCreateStructTest extends TestCase
{
    public function testDefaultProperties()
    {
        $conditionCreateStruct = new ConditionCreateStruct();

        self::assertNull($conditionCreateStruct->identifier);
        self::assertNull($conditionCreateStruct->value);
    }

    public function testSetProperties()
    {
        $conditionCreateStruct = new ConditionCreateStruct(
            array(
                'identifier' => 'condition',
                'value' => 42,
            )
        );

        self::assertEquals('condition', $conditionCreateStruct->identifier);
        self::assertEquals(42, $conditionCreateStruct->value);
    }
}
