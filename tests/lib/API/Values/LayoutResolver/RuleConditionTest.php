<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\Status;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

#[CoversClass(Condition::class)]
#[CoversClass(RuleCondition::class)]
final class RuleConditionTest extends TestCase
{
    public function testSetProperties(): void
    {
        $conditionType = new ConditionType1();

        $conditionUuid = Uuid::v4();
        $ruleUuid = Uuid::v4();

        $condition = RuleCondition::fromArray(
            [
                'id' => $conditionUuid,
                'status' => Status::Archived,
                'ruleId' => $ruleUuid,
                'conditionType' => $conditionType,
                'value' => 32,
            ],
        );

        self::assertSame($conditionUuid->toString(), $condition->id->toString());
        self::assertSame(Status::Archived, $condition->status);
        self::assertSame($ruleUuid->toString(), $condition->ruleId->toString());
        self::assertSame($conditionType, $condition->conditionType);
        self::assertSame(32, $condition->value);
    }
}
