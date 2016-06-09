<?php

namespace Netgen\BlockManager\Tests\Persistence\Values\Rule;

use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;

class ConditionTest extends \PHPUnit\Framework\TestCase
{
    public function testSetDefaultProperties()
    {
        $condition = new Condition();

        self::assertNull($condition->id);
        self::assertNull($condition->ruleId);
        self::assertNull($condition->identifier);
        self::assertNull($condition->value);
        self::assertNull($condition->status);
    }

    public function testSetProperties()
    {
        $condition = new Condition(
            array(
                'id' => 42,
                'ruleId' => 30,
                'identifier' => 'condition',
                'value' => 32,
                'status' => Rule::STATUS_PUBLISHED,
            )
        );

        self::assertEquals(42, $condition->id);
        self::assertEquals(30, $condition->ruleId);
        self::assertEquals('condition', $condition->identifier);
        self::assertEquals(32, $condition->value);
        self::assertEquals(Rule::STATUS_PUBLISHED, $condition->status);
    }
}
