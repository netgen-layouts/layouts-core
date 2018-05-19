<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use PHPUnit\Framework\TestCase;

final class TargetCreateStructTest extends TestCase
{
    public function testSetProperties()
    {
        $targetCreateStruct = new TargetCreateStruct(
            [
                'type' => 'target',
                'value' => 42,
            ]
        );

        $this->assertEquals('target', $targetCreateStruct->type);
        $this->assertEquals(42, $targetCreateStruct->value);
    }
}
