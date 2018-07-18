<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\Value;
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

        $this->assertSame(42, $target->id);
        $this->assertSame(30, $target->ruleId);
        $this->assertSame('target', $target->type);
        $this->assertSame(32, $target->value);
        $this->assertSame(Value::STATUS_PUBLISHED, $target->status);
    }
}
