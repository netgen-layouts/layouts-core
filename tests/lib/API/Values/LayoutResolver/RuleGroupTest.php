<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\ConditionList;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[CoversClass(RuleGroup::class)]
final class RuleGroupTest extends TestCase
{
    public function testSetProperties(): void
    {
        $rule1 = new Rule();
        $rule2 = new Rule();

        $condition = new RuleGroupCondition();

        $uuid = Uuid::uuid4();
        $parentUuid = Uuid::uuid4();

        $ruleGroup = RuleGroup::fromArray(
            [
                'id' => $uuid,
                'parentId' => $parentUuid,
                'name' => 'Name',
                'description' => 'Description',
                'priority' => 13,
                'isEnabled' => true,
                'rules' => RuleList::fromArray([$rule1, $rule2]),
                'conditions' => ConditionList::fromArray([$condition]),
            ],
        );

        self::assertSame($uuid->toString(), $ruleGroup->id->toString());
        self::assertInstanceOf(UuidInterface::class, $ruleGroup->parentId);
        self::assertSame($parentUuid->toString(), $ruleGroup->parentId->toString());
        self::assertSame('Name', $ruleGroup->name);
        self::assertSame('Description', $ruleGroup->description);
        self::assertSame(13, $ruleGroup->priority);
        self::assertTrue($ruleGroup->isEnabled);

        self::assertCount(2, $ruleGroup->rules);
        self::assertCount(1, $ruleGroup->conditions);

        self::assertSame($rule1, $ruleGroup->rules[0]);
        self::assertSame($rule2, $ruleGroup->rules[1]);

        self::assertSame($condition, $ruleGroup->conditions[0]);
    }
}
