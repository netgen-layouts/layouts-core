<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $target = Target::fromArray(
            [
                'id' => 42,
                'uuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'ruleId' => 30,
                'ruleUuid' => 'f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee',
                'type' => 'target',
                'value' => 32,
                'status' => Value::STATUS_PUBLISHED,
            ],
        );

        self::assertSame(42, $target->id);
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $target->uuid);
        self::assertSame(30, $target->ruleId);
        self::assertSame('f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee', $target->ruleUuid);
        self::assertSame('target', $target->type);
        self::assertSame(32, $target->value);
        self::assertSame(Value::STATUS_PUBLISHED, $target->status);
    }
}
