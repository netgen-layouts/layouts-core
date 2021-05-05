<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RuleGroupConditionTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Condition::getConditionType
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Condition::getValue
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition::getRuleGroupId
     */
    public function testSetProperties(): void
    {
        $conditionType = new ConditionType1();

        $conditionUuid = Uuid::uuid4();
        $ruleGroupUuid = Uuid::uuid4();

        $condition = RuleGroupCondition::fromArray(
            [
                'id' => $conditionUuid,
                'ruleGroupId' => $ruleGroupUuid,
                'conditionType' => $conditionType,
                'value' => 32,
            ],
        );

        self::assertSame($conditionUuid->toString(), $condition->getId()->toString());
        self::assertSame($ruleGroupUuid->toString(), $condition->getRuleGroupId()->toString());
        self::assertSame($conditionType, $condition->getConditionType());
        self::assertSame(32, $condition->getValue());
    }
}
