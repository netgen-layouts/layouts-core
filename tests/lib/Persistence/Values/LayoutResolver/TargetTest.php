<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\LayoutResolver\Rule;

use Netgen\BlockManager\Persistence\Values\Value;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use PHPUnit\Framework\TestCase;

class TargetTest extends TestCase
{
    public function testSetDefaultProperties()
    {
        $target = new Target();

        $this->assertNull($target->id);
        $this->assertNull($target->ruleId);
        $this->assertNull($target->type);
        $this->assertNull($target->value);
        $this->assertNull($target->status);
    }

    public function testSetProperties()
    {
        $target = new Target(
            array(
                'id' => 42,
                'ruleId' => 30,
                'type' => 'target',
                'value' => 32,
                'status' => Value::STATUS_PUBLISHED,
            )
        );

        $this->assertEquals(42, $target->id);
        $this->assertEquals(30, $target->ruleId);
        $this->assertEquals('target', $target->type);
        $this->assertEquals(32, $target->value);
        $this->assertEquals(Value::STATUS_PUBLISHED, $target->status);
    }
}
