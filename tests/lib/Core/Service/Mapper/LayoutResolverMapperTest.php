<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition as APICondition;
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

        self::assertSame(3, $rule->getId());
        self::assertInstanceOf(Layout::class, $rule->getLayout());
        self::assertSame(1, $rule->getLayout()->getId());
        self::assertTrue($rule->isPublished());
        self::assertTrue($rule->isEnabled());
        self::assertSame(12, $rule->getPriority());
        self::assertSame('Comment', $rule->getComment());

        self::assertNotEmpty($rule->getTargets());
        self::assertContainsOnlyInstancesOf(APITarget::class, $rule->getTargets());

        self::assertNotEmpty($rule->getConditions());
        self::assertContainsOnlyInstancesOf(APICondition::class, $rule->getConditions());
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

        self::assertNull($rule->getLayout());
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

        self::assertSame(
            $this->targetTypeRegistry->getTargetType('target1'),
            $target->getTargetType()
        );

        self::assertSame(1, $target->getId());
        self::assertTrue($target->isPublished());
        self::assertSame(42, $target->getRuleId());
        self::assertSame(42, $target->getValue());
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

        self::assertInstanceOf(NullTargetType::class, $target->getTargetType());

        self::assertSame(1, $target->getId());
        self::assertTrue($target->isPublished());
        self::assertSame(42, $target->getRuleId());
        self::assertSame(42, $target->getValue());
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
                'type' => 'condition1',
                'value' => 42,
            ]
        );

        $condition = $this->mapper->mapCondition($persistenceCondition);

        self::assertSame(
            $this->conditionTypeRegistry->getConditionType('condition1'),
            $condition->getConditionType()
        );

        self::assertSame(1, $condition->getId());
        self::assertTrue($condition->isPublished());
        self::assertSame(42, $condition->getRuleId());
        self::assertSame(42, $condition->getValue());
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

        self::assertInstanceOf(NullConditionType::class, $condition->getConditionType());

        self::assertSame(1, $condition->getId());
        self::assertTrue($condition->isPublished());
        self::assertSame(42, $condition->getRuleId());
        self::assertSame(42, $condition->getValue());
    }
}
