<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\Value;
use Netgen\Layouts\Core\Mapper\LayoutResolverMapper;
use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class LayoutResolverMapperTestBase extends CoreTestCase
{
    private LayoutResolverMapper $mapper;

    protected function setUp(): void
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
                'uuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'status' => Value::STATUS_PUBLISHED,
                'ruleGroupId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'enabled' => true,
                'priority' => 12,
                'description' => 'Description',
            ],
        );

        $rule = $this->mapper->mapRule($persistenceRule);

        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $rule->getId()->toString());
        self::assertSame(RuleGroup::ROOT_UUID, $rule->getRuleGroupId()->toString());
        self::assertInstanceOf(Layout::class, $rule->getLayout());
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $rule->getLayout()->getId()->toString());
        self::assertTrue($rule->isPublished());
        self::assertTrue($rule->isEnabled());
        self::assertSame(12, $rule->getPriority());
        self::assertSame('Description', $rule->getDescription());

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
                'id' => 3,
                'uuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'status' => Value::STATUS_PUBLISHED,
                'ruleGroupId' => 1,
                'layoutUuid' => Uuid::uuid4()->toString(),
                'enabled' => true,
                'priority' => 12,
                'description' => 'Description',
            ],
        );

        $rule = $this->mapper->mapRule($persistenceRule);

        self::assertNull($rule->getLayout());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapRuleGroup
     */
    public function testMapRuleGroup(): void
    {
        $persistenceRuleGroup = RuleGroup::fromArray(
            [
                'id' => 2,
                'uuid' => 'b4f85f38-de3f-4af7-9a5f-21df63a49da9',
                'status' => Value::STATUS_PUBLISHED,
                'depth' => 1,
                'path' => '/1/2/',
                'parentId' => 1,
                'parentUuid' => RuleGroup::ROOT_UUID,
                'name' => 'Name',
                'description' => 'Description',
                'enabled' => true,
                'priority' => 1,
            ],
        );

        $ruleGroup = $this->mapper->mapRuleGroup($persistenceRuleGroup);

        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $ruleGroup->getId()->toString());
        self::assertInstanceOf(UuidInterface::class, $ruleGroup->getParentId());
        self::assertSame(RuleGroup::ROOT_UUID, $ruleGroup->getParentId()->toString());
        self::assertTrue($ruleGroup->isPublished());
        self::assertSame('Name', $ruleGroup->getName());
        self::assertSame('Description', $ruleGroup->getDescription());
        self::assertTrue($ruleGroup->isEnabled());
        self::assertSame(1, $ruleGroup->getPriority());

        self::assertCount(2, $ruleGroup->getRules());
        self::assertCount(2, $ruleGroup->getConditions());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapTarget
     */
    public function testMapTarget(): void
    {
        $persistenceTarget = Target::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'target1',
                'value' => 42,
            ],
        );

        $target = $this->mapper->mapTarget($persistenceTarget);

        self::assertSame(
            $this->targetTypeRegistry->getTargetType('target1'),
            $target->getTargetType(),
        );

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $target->getId()->toString());
        self::assertTrue($target->isPublished());
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $target->getRuleId()->toString());
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
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'unknown',
                'value' => 42,
            ],
        );

        $target = $this->mapper->mapTarget($persistenceTarget);

        self::assertInstanceOf(NullTargetType::class, $target->getTargetType());

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $target->getId()->toString());
        self::assertTrue($target->isPublished());
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $target->getRuleId()->toString());
        self::assertSame(42, $target->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapRuleCondition
     */
    public function testMapRuleCondition(): void
    {
        $persistenceCondition = RuleCondition::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'condition1',
                'value' => 42,
            ],
        );

        $condition = $this->mapper->mapRuleCondition($persistenceCondition);

        self::assertSame(
            $this->conditionTypeRegistry->getConditionType('condition1'),
            $condition->getConditionType(),
        );

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $condition->getId()->toString());
        self::assertTrue($condition->isPublished());
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $condition->getRuleId()->toString());
        self::assertSame(42, $condition->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapRuleCondition
     */
    public function testMapRuleConditionWithInvalidConditionType(): void
    {
        $persistenceCondition = RuleCondition::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'ruleId' => 42,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'unknown',
                'value' => 42,
            ],
        );

        $condition = $this->mapper->mapRuleCondition($persistenceCondition);

        self::assertInstanceOf(NullConditionType::class, $condition->getConditionType());

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $condition->getId()->toString());
        self::assertTrue($condition->isPublished());
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $condition->getRuleId()->toString());
        self::assertSame(42, $condition->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapRuleGroupCondition
     */
    public function testMapRuleGroupCondition(): void
    {
        $persistenceCondition = RuleGroupCondition::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'ruleGroupId' => 42,
                'ruleGroupUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'condition1',
                'value' => 42,
            ],
        );

        $condition = $this->mapper->mapRuleGroupCondition($persistenceCondition);

        self::assertSame(
            $this->conditionTypeRegistry->getConditionType('condition1'),
            $condition->getConditionType(),
        );

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $condition->getId()->toString());
        self::assertTrue($condition->isPublished());
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $condition->getRuleGroupId()->toString());
        self::assertSame(42, $condition->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\LayoutResolverMapper::mapRuleGroupCondition
     */
    public function testMapRuleGroupConditionWithInvalidConditionType(): void
    {
        $persistenceCondition = RuleGroupCondition::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => Value::STATUS_PUBLISHED,
                'ruleGroupId' => 42,
                'ruleGroupUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'unknown',
                'value' => 42,
            ],
        );

        $condition = $this->mapper->mapRuleGroupCondition($persistenceCondition);

        self::assertInstanceOf(NullConditionType::class, $condition->getConditionType());

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $condition->getId()->toString());
        self::assertTrue($condition->isPublished());
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $condition->getRuleGroupId()->toString());
        self::assertSame(42, $condition->getValue());
    }
}
