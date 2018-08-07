<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Persistence\Values\LayoutResolver;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class RuleTest extends TestCase
{
    public function testSetProperties(): void
    {
        $rule = Rule::fromArray(
            [
                'id' => 43,
                'layoutId' => 25,
                'enabled' => true,
                'priority' => 3,
                'comment' => 'Comment',
                'status' => Value::STATUS_DRAFT,
            ]
        );

        self::assertSame(43, $rule->id);
        self::assertSame(25, $rule->layoutId);
        self::assertTrue($rule->enabled);
        self::assertSame(3, $rule->priority);
        self::assertSame('Comment', $rule->comment);
        self::assertSame(Value::STATUS_DRAFT, $rule->status);
    }
}
