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
        $condition = Condition::fromArray(
            [
                'id' => 42,
                'ruleId' => 30,
                'type' => 'condition',
                'value' => 32,
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        self::assertSame(42, $condition->id);
        self::assertSame(30, $condition->ruleId);
        self::assertSame('condition', $condition->type);
        self::assertSame(32, $condition->value);
        self::assertSame(Value::STATUS_PUBLISHED, $condition->status);
    }
}
