<?php

namespace Netgen\BlockManager\Tests\Core\Values\LayoutResolver;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Core\Values\LayoutResolver\Condition;

class ConditionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getValue
     */
    public function testSetDefaultProperties()
    {
        $condition = new Condition();

        self::assertNull($condition->getId());
        self::assertNull($condition->getStatus());
        self::assertNull($condition->getRuleId());
        self::assertNull($condition->getIdentifier());
        self::assertNull($condition->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::__construct
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getStatus
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getRuleId
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getIdentifier
     * @covers \Netgen\BlockManager\Core\Values\LayoutResolver\Condition::getValue
     */
    public function testSetProperties()
    {
        $condition = new Condition(
            array(
                'id' => 42,
                'status' => Rule::STATUS_PUBLISHED,
                'ruleId' => 30,
                'identifier' => 'condition',
                'value' => 32,
            )
        );

        self::assertEquals(42, $condition->getId());
        self::assertEquals(Rule::STATUS_PUBLISHED, $condition->getStatus());
        self::assertEquals(30, $condition->getRuleId());
        self::assertEquals('condition', $condition->getIdentifier());
        self::assertEquals(32, $condition->getValue());
    }
}
