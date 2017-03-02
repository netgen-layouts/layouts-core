<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class LayoutResolverMapperTest extends ServiceTestCase
{
    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        parent::setUp();

        $this->layoutResolverMapper = $this->createLayoutResolverMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\Mapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapRule
     */
    public function testMapRule()
    {
        $persistenceRule = new Rule(
            array(
                'id' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'layoutId' => 1,
                'enabled' => true,
                'priority' => 12,
                'comment' => 'Comment',
            )
        );

        $rule = $this->layoutResolverMapper->mapRule($persistenceRule);

        $this->assertInstanceOf(APIRule::class, $rule);
        $this->assertEquals(3, $rule->getId());
        $this->assertInstanceOf(Layout::class, $rule->getLayout());
        $this->assertEquals(1, $rule->getLayout()->getId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $rule->getStatus());
        $this->assertTrue($rule->isEnabled());
        $this->assertEquals(12, $rule->getPriority());
        $this->assertEquals('Comment', $rule->getComment());
        $this->assertTrue($rule->isPublished());

        $this->assertNotEmpty($rule->getTargets());

        foreach ($rule->getTargets() as $target) {
            $this->assertInstanceOf(APITarget::class, $target);
        }

        $this->assertNotEmpty($rule->getConditions());

        foreach ($rule->getConditions() as $condition) {
            $this->assertInstanceOf(APICondition::class, $condition);
        }
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapRule
     */
    public function testMapRuleWithNonExistingLayout()
    {
        $persistenceRule = new Rule(
            array(
                'layoutId' => 99999,
            )
        );

        $rule = $this->layoutResolverMapper->mapRule($persistenceRule);

        $this->assertInstanceOf(APIRule::class, $rule);
        $this->assertNull($rule->getLayout());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapTarget
     */
    public function testMapTarget()
    {
        $persistenceTarget = new Target(
            array(
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'type' => 'target',
                'value' => 42,
            )
        );

        $target = $this->layoutResolverMapper->mapTarget($persistenceTarget);

        $this->assertEquals(
            $this->targetTypeRegistry->getTargetType('target'),
            $target->getTargetType()
        );

        $this->assertInstanceOf(APITarget::class, $target);
        $this->assertEquals(1, $target->getId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $target->getStatus());
        $this->assertEquals(42, $target->getRuleId());
        $this->assertEquals(42, $target->getValue());
        $this->assertTrue($target->isPublished());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapCondition
     */
    public function testMapCondition()
    {
        $persistenceCondition = new Condition(
            array(
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'type' => 'condition',
                'value' => 42,
            )
        );

        $condition = $this->layoutResolverMapper->mapCondition($persistenceCondition);

        $this->assertEquals(
            $this->conditionTypeRegistry->getConditionType('condition'),
            $condition->getConditionType()
        );

        $this->assertInstanceOf(APICondition::class, $condition);
        $this->assertEquals(1, $condition->getId());
        $this->assertEquals(Value::STATUS_PUBLISHED, $condition->getStatus());
        $this->assertEquals(42, $condition->getRuleId());
        $this->assertEquals(42, $condition->getValue());
        $this->assertTrue($condition->isPublished());
    }
}
