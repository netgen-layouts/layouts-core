<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\ConditionUpdateStruct;

class ConditionUpdateStructTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultProperties()
    {
        $conditionUpdateStruct = new ConditionUpdateStruct();

        self::assertNull($conditionUpdateStruct->value);
    }

    public function testSetProperties()
    {
        $conditionUpdateStruct = new ConditionUpdateStruct(
            array(
                'value' => 42,
            )
        );

        self::assertEquals(42, $conditionUpdateStruct->value);
    }
}
