<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    public function testSetProperties(): void
    {
        $condition = new Condition(
            [
                'id' => 42,
                'ruleId' => 30,
                'type' => 'condition',
                'value' => 32,
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        $this->assertSame(42, $condition->id);
        $this->assertSame(30, $condition->ruleId);
        $this->assertSame('condition', $condition->type);
        $this->assertSame(32, $condition->value);
        $this->assertSame(Value::STATUS_PUBLISHED, $condition->status);
    }
}
