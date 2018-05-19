<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    public function testSetProperties()
    {
        $target = new Target(
            [
                'id' => 42,
                'ruleId' => 30,
                'type' => 'target',
                'value' => 32,
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        $this->assertEquals(42, $target->id);
        $this->assertEquals(30, $target->ruleId);
        $this->assertEquals('target', $target->type);
        $this->assertEquals(32, $target->value);
        $this->assertEquals(Value::STATUS_PUBLISHED, $target->status);
    }
}
