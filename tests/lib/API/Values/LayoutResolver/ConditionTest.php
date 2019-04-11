<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\LayoutResolver;

use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Tests\Layout\Resolver\Stubs\ConditionType1;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    public function testInstance(): void
    {
        self::assertInstanceOf(Value::class, new Condition());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Condition::getConditionType
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\Layouts\API\Values\LayoutResolver\Condition::getValue
     */
    public function testSetProperties(): void
    {
        $conditionType = new ConditionType1();

        $condition = Condition::fromArray(
            [
                'id' => 42,
                'ruleId' => 30,
                'conditionType' => $conditionType,
                'value' => 32,
            ]
        );

        self::assertSame(42, $condition->getId());
        self::assertSame(30, $condition->getRuleId());
        self::assertSame($conditionType, $condition->getConditionType());
        self::assertSame(32, $condition->getValue());
    }
}
