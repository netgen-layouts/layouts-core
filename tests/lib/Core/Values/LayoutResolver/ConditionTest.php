<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(Value::class, new Condition());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getConditionType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getValue
     */
    public function testSetProperties()
    {
        $condition = new Condition(
            [
                'id' => 42,
                'ruleId' => 30,
                'conditionType' => new ConditionType('condition'),
                'value' => 32,
            ]
        );

        $this->assertEquals(42, $condition->getId());
        $this->assertEquals(30, $condition->getRuleId());
        $this->assertEquals(new ConditionType('condition'), $condition->getConditionType());
        $this->assertEquals(32, $condition->getValue());
    }
}
