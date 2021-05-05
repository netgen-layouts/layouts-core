<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Persistence\Values\LayoutResolver;

use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\Value;
use PHPUnit\Framework\TestCase;

final class RuleGroupTest extends TestCase
{
    /**
     * @coversNothing
     */
    public function testSetProperties(): void
    {
        $ruleGroup = RuleGroup::fromArray(
            [
                'id' => 43,
                'uuid' => 'f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee',
                'depth' => 2,
                'path' => '/42/43/',
                'parentId' => 42,
                'parentUuid' => 'c3527744-3285-416e-9a4c-5bb753d43e35',
                'name' => 'Name',
                'description' => 'Description',
                'enabled' => true,
                'priority' => 3,
                'status' => Value::STATUS_DRAFT,
            ],
        );

        self::assertSame(43, $ruleGroup->id);
        self::assertSame('f4e3d39e-42ba-59b4-82ff-bc38dd6bf7ee', $ruleGroup->uuid);
        self::assertSame(2, $ruleGroup->depth);
        self::assertSame('/42/43/', $ruleGroup->path);
        self::assertSame(42, $ruleGroup->parentId);
        self::assertSame('c3527744-3285-416e-9a4c-5bb753d43e35', $ruleGroup->parentUuid);
        self::assertSame('Name', $ruleGroup->name);
        self::assertSame('Description', $ruleGroup->description);
        self::assertTrue($ruleGroup->enabled);
        self::assertSame(3, $ruleGroup->priority);
        self::assertSame(Value::STATUS_DRAFT, $ruleGroup->status);
    }
}
