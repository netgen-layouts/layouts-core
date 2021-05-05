<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class RuleConditionTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $condition = RuleCondition::fromArray(
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'ruleId' => 30,
                'ruleUuid' => 'f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee',
                'type' => 'condition',
                'value' => 32,
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        self::assertSame(42, $condition->id);
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $condition->uuid);
        self::assertSame(30, $condition->ruleId);
        self::assertSame('f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee', $condition->ruleUuid);
        self::assertSame('condition', $condition->type);
        self::assertSame(32, $condition->value);
        self::assertSame(Value::STATUS_PUBLISHED, $condition->status);
    }
}
