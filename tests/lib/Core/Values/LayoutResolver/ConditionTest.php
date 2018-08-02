<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType1;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Value::class, new Condition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getConditionType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getValue
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

        $this->assertSame(42, $condition->getId());
        $this->assertSame(30, $condition->getRuleId());
        $this->assertSame($conditionType, $condition->getConditionType());
        $this->assertSame(32, $condition->getValue());
    }
}
