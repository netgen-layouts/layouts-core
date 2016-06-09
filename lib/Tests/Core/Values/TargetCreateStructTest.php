<?php

namespace Netgen\BlockManager\Tests\Core\Values;

use Netgen\BlockManager\API\Values\TargetCreateStruct;

class TargetCreateStructTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaultProperties()
    {
        $targetCreateStruct = new TargetCreateStruct();

        self::assertNull($targetCreateStruct->identifier);
        self::assertNull($targetCreateStruct->value);
    }

    public function testSetProperties()
    {
        $targetCreateStruct = new TargetCreateStruct(
            array(
                'identifier' => 'target',
                'value' => 42,
            )
        );

        self::assertEquals('target', $targetCreateStruct->identifier);
        self::assertEquals(42, $targetCreateStruct->value);
    }
}
