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

        $this->assertSame(43, $rule->id);
        $this->assertSame(25, $rule->layoutId);
        $this->assertTrue($rule->enabled);
        $this->assertSame(3, $rule->priority);
        $this->assertSame('Comment', $rule->comment);
        $this->assertSame(Value::STATUS_DRAFT, $rule->status);
    }
}
