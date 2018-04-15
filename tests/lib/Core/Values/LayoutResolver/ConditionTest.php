<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Tests\Layout\Resolver\Stubs\ConditionType;
use PHPUnit\Framework\TestCase;

final class ConditionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getConditionType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getValue
     */
    public function testSetDefaultProperties()
    {
        $condition = new Condition();

        $this->assertNull($condition->getId());
        $this->assertNull($condition->getStatus());
        $this->assertNull($condition->getRuleId());
        $this->assertNull($condition->getConditionType());
        $this->assertNull($condition->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getConditionType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getValue
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::isPublished
     */
    public function testSetProperties()
    {
        $condition = new Condition(
            array(
                'id' => 42,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 30,
                'conditionType' => new ConditionType('condition'),
                'value' => 32,
            )
        );

        $this->assertEquals(42, $condition->getId());
        $this->assertTrue($condition->isPublished());
        $this->assertEquals(30, $condition->getRuleId());
        $this->assertEquals(new ConditionType('condition'), $condition->getConditionType());
        $this->assertEquals(32, $condition->getValue());
        $this->assertTrue($condition->isPublished());
    }
}
