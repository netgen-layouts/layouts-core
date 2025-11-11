<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\Status;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class RuleTest extends TestCase
{
    public function testSetProperties(): void
    {
        $rule = Rule::fromArray(
            [
                'id' => 43,
                'uuid' => 'f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee',
                'ruleGroupId' => 42,
                'layoutUuid' => '4adf0f00-f6c2-5297-9f96-039bfabe8d3b',
                'enabled' => true,
                'priority' => 3,
                'description' => 'Description',
                'status' => Status::Draft,
            ],
        );

        self::assertSame(43, $rule->id);
        self::assertSame('f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee', $rule->uuid);
        self::assertSame(42, $rule->ruleGroupId);
        self::assertSame('4adf0f00-f6c2-5297-9f96-039bfabe8d3b', $rule->layoutUuid);
        self::assertTrue($rule->enabled);
        self::assertSame(3, $rule->priority);
        self::assertSame('Description', $rule->description);
        self::assertSame(Status::Draft, $rule->status);
    }
}
