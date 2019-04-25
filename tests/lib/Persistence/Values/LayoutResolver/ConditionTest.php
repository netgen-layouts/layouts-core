<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    public function testSetProperties(): void
    {
        $condition = Condition::fromArray(
            [
                'id' => 42,
                'ruleId' => 30,
                'ruleUuid' => 'f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee',
                'type' => 'condition',
                'value' => 32,
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        self::assertSame(42, $condition->id);
        self::assertSame(30, $condition->ruleId);
        self::assertSame('f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee', $condition->ruleUuid);
        self::assertSame('condition', $condition->type);
        self::assertSame(32, $condition->value);
        self::assertSame(Value::STATUS_PUBLISHED, $condition->status);
    }
}
