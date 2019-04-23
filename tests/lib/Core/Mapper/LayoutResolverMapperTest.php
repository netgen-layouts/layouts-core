<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Ramsey\Uuid\Uuid;

abstract class LayoutResolverMapperTest extends CoreTestCase
{
    /**
     * @var \Netgen\Layouts\Core\Mapper\LayoutResolverMapper
     */
    private $mapper;

    public function setUp(): void
    {
        parent::setUp();

        $this->mapper = $this->createLayoutResolverMapper();
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapRule
     */
    public function testMapRule(): void
    {
        $persistenceRule = Rule::fromArray(
            [
                'id' => 3,
                'status' => Value::STATUS_PUBLISHED,
                'layoutId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'enabled' => true,
                'priority' => 12,
                'comment' => 'Comment',
            ]
        );

        $rule = $this->mapper->mapRule($persistenceRule);

        self::assertSame(3, $rule->getId());
        self::assertInstanceOf(Layout::class, $rule->getLayout());
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $rule->getLayout()->getId()->toString());
        self::assertTrue($rule->isPublished());
        self::assertTrue($rule->isEnabled());
        self::assertSame(12, $rule->getPriority());
        self::assertSame('Comment', $rule->getComment());

        self::assertCount(2, $rule->getTargets());
        self::assertCount(2, $rule->getConditions());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapRule
     */
    public function testMapRuleWithNonExistingLayout(): void
    {
        $persistenceRule = Rule::fromArray(
            [
                'layoutId' => Uuid::uuid4(),
            ]
        );

        $rule = $this->mapper->mapRule($persistenceRule);

        self::assertNull($rule->getLayout());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapTarget
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
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapTarget
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
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapCondition
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
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapCondition
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
