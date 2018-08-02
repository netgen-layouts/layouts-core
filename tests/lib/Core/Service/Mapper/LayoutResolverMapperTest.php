<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule as APIRule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target as APITarget;
use Netgen\BlockManager\API\Values\Value;
use Netgen\BlockManager\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\BlockManager\Layout\Resolver\TargetType\NullTargetType;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Tests\Core\Service\ServiceTestCase;

abstract class LayoutResolverMapperTest extends ServiceTestCase
{
    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper
     */
    private $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createLayoutResolverMapper();
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapRule
     */
    public function testMapRule(): void
    {
        $persistenceRule = Rule::fromArray(
            [
                'id' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'layoutId' => 1,
                'enabled' => true,
                'priority' => 12,
                'comment' => 'Comment',
            ]
        );

        $rule = $this->mapper->mapRule($persistenceRule);

        $this->assertInstanceOf(APIRule::class, $rule);
        $this->assertSame(3, $rule->getId());
        $this->assertInstanceOf(Layout::class, $rule->getLayout());
        $this->assertSame(1, $rule->getLayout()->getId());
        $this->assertTrue($rule->isPublished());
        $this->assertTrue($rule->isEnabled());
        $this->assertSame(12, $rule->getPriority());
        $this->assertSame('Comment', $rule->getComment());

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
    public function testMapRuleWithNonExistingLayout(): void
    {
        $persistenceRule = Rule::fromArray(
            [
                'layoutId' => 99999,
            ]
        );

        $rule = $this->mapper->mapRule($persistenceRule);

        $this->assertInstanceOf(APIRule::class, $rule);
        $this->assertNull($rule->getLayout());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapTarget
     */
    public function testMapTarget(): void
    {
        $persistenceTarget = Target::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'type' => 'target1',
                'value' => 42,
            ]
        );

        $target = $this->mapper->mapTarget($persistenceTarget);

        $this->assertSame(
            $this->targetTypeRegistry->getTargetType('target1'),
            $target->getTargetType()
        );

        $this->assertInstanceOf(APITarget::class, $target);
        $this->assertSame(1, $target->getId());
        $this->assertTrue($target->isPublished());
        $this->assertSame(42, $target->getRuleId());
        $this->assertSame(42, $target->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapTarget
     */
    public function testMapTargetWithInvalidTargetType(): void
    {
        $persistenceTarget = Target::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'type' => 'unknown',
                'value' => 42,
            ]
        );

        $target = $this->mapper->mapTarget($persistenceTarget);

        $this->assertInstanceOf(NullTargetType::class, $target->getTargetType());

        $this->assertInstanceOf(APITarget::class, $target);
        $this->assertSame(1, $target->getId());
        $this->assertTrue($target->isPublished());
        $this->assertSame(42, $target->getRuleId());
        $this->assertSame(42, $target->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapCondition
     */
    public function testMapCondition(): void
    {
        $persistenceCondition = Condition::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'type' => 'my_condition',
                'value' => 42,
            ]
        );

        $condition = $this->mapper->mapCondition($persistenceCondition);

        $this->assertSame(
            $this->conditionTypeRegistry->getConditionType('my_condition'),
            $condition->getConditionType()
        );

        $this->assertInstanceOf(APICondition::class, $condition);
        $this->assertSame(1, $condition->getId());
        $this->assertTrue($condition->isPublished());
        $this->assertSame(42, $condition->getRuleId());
        $this->assertSame(42, $condition->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\LayoutResolverMapper::mapCondition
     */
    public function testMapConditionWithInvalidConditionType(): void
    {
        $persistenceCondition = Condition::fromArray(
            [
                'id' => 1,
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'type' => 'unknown',
                'value' => 42,
            ]
        );

        $condition = $this->mapper->mapCondition($persistenceCondition);

        $this->assertInstanceOf(NullConditionType::class, $condition->getConditionType());

        $this->assertInstanceOf(APICondition::class, $condition);
        $this->assertSame(1, $condition->getId());
        $this->assertTrue($condition->isPublished());
        $this->assertSame(42, $condition->getRuleId());
        $this->assertSame(42, $condition->getValue());
    }
}
