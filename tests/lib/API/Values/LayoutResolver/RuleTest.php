<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionList;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Rule::class)]
final class RuleTest extends TestCase
{
    public function testSetProperties(): void
    {
        $target1 = new Target();
        $target2 = new Target();

        $condition = new RuleCondition();

        $layout = new Layout();

        $uuid = Uuid::v4();
        $ruleGroupUuid = Uuid::v4();

        $rule = Rule::fromArray(
            [
                'id' => $uuid,
                'ruleGroupId' => $ruleGroupUuid,
                'layout' => $layout,
                'priority' => 13,
                'isEnabled' => true,
                'description' => 'Description',
                'targets' => TargetList::fromArray([$target1, $target2]),
                'conditions' => ConditionList::fromArray([$condition]),
            ],
        );

        self::assertSame($uuid->toString(), $rule->id->toString());
        self::assertSame($ruleGroupUuid->toString(), $rule->ruleGroupId->toString());
        self::assertSame($layout, $rule->layout);
        self::assertSame(13, $rule->priority);
        self::assertTrue($rule->isEnabled);
        self::assertSame('Description', $rule->description);

        self::assertCount(2, $rule->targets);
        self::assertCount(1, $rule->conditions);

        self::assertSame($target1, $rule->targets[0]);
        self::assertSame($target2, $rule->targets[1]);

        self::assertSame($condition, $rule->conditions[0]);
    }
}
