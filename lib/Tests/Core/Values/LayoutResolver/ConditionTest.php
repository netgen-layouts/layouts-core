<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getValue
     */
    public function testSetDefaultProperties()
    {
        $condition = new Condition();

        $this->assertNull($condition->getId());
        $this->assertNull($condition->getStatus());
        $this->assertNull($condition->getRuleId());
        $this->assertNull($condition->getType());
        $this->assertNull($condition->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getType
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getValue
     */
    public function testSetProperties()
    {
        $condition = new Condition(
            array(
                'id' => 42,
                'status' => Rule::STATUS_PUBLISHED,
                'ruleId' => 30,
                'type' => 'condition',
                'value' => 32,
            )
        );

        $this->assertEquals(42, $condition->getId());
        $this->assertEquals(Rule::STATUS_PUBLISHED, $condition->getStatus());
        $this->assertEquals(30, $condition->getRuleId());
        $this->assertEquals('condition', $condition->getType());
        $this->assertEquals(32, $condition->getValue());
    }
}
