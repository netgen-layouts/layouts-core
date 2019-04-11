<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class TargetTest extends TestCase
{
    public function testSetProperties(): void
    {
        $target = Target::fromArray(
            [
                'id' => 42,
                'ruleId' => 30,
                'type' => 'target',
                'value' => 32,
                'status' => Value::STATUS_PUBLISHED,
            ]
        );

        self::assertSame(42, $target->id);
        self::assertSame(30, $target->ruleId);
        self::assertSame('target', $target->type);
        self::assertSame(32, $target->value);
        self::assertSame(Value::STATUS_PUBLISHED, $target->status);
    }
}
