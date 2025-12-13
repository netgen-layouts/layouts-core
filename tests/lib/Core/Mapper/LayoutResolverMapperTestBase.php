<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use Netgen\Layouts\Tests\Core\CoreTestCase;
use Symfony\Component\Uid\Uuid;

abstract class LayoutResolverMapperTestBase extends CoreTestCase
{
    final public function testMapRule(): void
    {
        $persistenceRule = Rule::fromArray(
            [
                'id' => 3,
                'uuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'status' => PersistenceStatus::Published,
                'ruleGroupId' => 1,
                'layoutUuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'isEnabled' => true,
                'priority' => 12,
                'description' => 'Description',
            ],
        );

        $rule = $this->layoutResolverMapper->mapRule($persistenceRule);

        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $rule->id->toString());
        self::assertSame(RuleGroup::ROOT_UUID, $rule->ruleGroupId->toString());
        self::assertInstanceOf(Layout::class, $rule->layout);
        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $rule->layout->id->toString());
        self::assertTrue($rule->isPublished);
        self::assertTrue($rule->isEnabled);
        self::assertSame(12, $rule->priority);
        self::assertSame('Description', $rule->description);

        self::assertCount(2, $rule->targets);
        self::assertCount(2, $rule->conditions);
    }

    final public function testMapRuleWithNonExistingLayout(): void
    {
        $persistenceRule = Rule::fromArray(
            [
                'id' => 3,
                'uuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'status' => PersistenceStatus::Published,
                'ruleGroupId' => 1,
                'layoutUuid' => Uuid::v4()->toString(),
                'isEnabled' => true,
                'priority' => 12,
                'description' => 'Description',
            ],
        );

        $rule = $this->layoutResolverMapper->mapRule($persistenceRule);

        self::assertNull($rule->layout);
    }

    final public function testMapRuleGroup(): void
    {
        $persistenceRuleGroup = RuleGroup::fromArray(
            [
                'id' => 2,
                'uuid' => 'b4f85f38-de3f-4af7-9a5f-21df63a49da9',
                'status' => PersistenceStatus::Published,
                'depth' => 1,
                'path' => '/1/2/',
                'parentId' => 1,
                'parentUuid' => RuleGroup::ROOT_UUID,
                'name' => 'Name',
                'description' => 'Description',
                'isEnabled' => true,
                'priority' => 1,
            ],
        );

        $ruleGroup = $this->layoutResolverMapper->mapRuleGroup($persistenceRuleGroup);

        self::assertSame('b4f85f38-de3f-4af7-9a5f-21df63a49da9', $ruleGroup->id->toString());
        self::assertInstanceOf(Uuid::class, $ruleGroup->parentId);
        self::assertSame(RuleGroup::ROOT_UUID, $ruleGroup->parentId->toString());
        self::assertTrue($ruleGroup->isPublished);
        self::assertSame('Name', $ruleGroup->name);
        self::assertSame('Description', $ruleGroup->description);
        self::assertTrue($ruleGroup->isEnabled);
        self::assertSame(1, $ruleGroup->priority);

        self::assertCount(2, $ruleGroup->rules);
        self::assertCount(2, $ruleGroup->conditions);
    }

    final public function testMapTarget(): void
    {
        $persistenceTarget = Target::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'ruleId' => 42,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'target1',
                'value' => 42,
            ],
        );

        $target = $this->layoutResolverMapper->mapTarget($persistenceTarget);

        self::assertSame(
            $this->targetTypeRegistry->getTargetType('target1'),
            $target->targetType,
        );

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $target->id->toString());
        self::assertTrue($target->isPublished);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $target->ruleId->toString());
        self::assertSame(42, $target->value);
    }

    final public function testMapTargetWithInvalidTargetType(): void
    {
        $persistenceTarget = Target::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'ruleId' => 42,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'unknown',
                'value' => 42,
            ],
        );

        $target = $this->layoutResolverMapper->mapTarget($persistenceTarget);

        self::assertInstanceOf(NullTargetType::class, $target->targetType);

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $target->id->toString());
        self::assertTrue($target->isPublished);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $target->ruleId->toString());
        self::assertSame(42, $target->value);
    }

    final public function testMapRuleCondition(): void
    {
        $persistenceCondition = RuleCondition::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'ruleId' => 42,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'condition1',
                'value' => 42,
            ],
        );

        $condition = $this->layoutResolverMapper->mapRuleCondition($persistenceCondition);

        self::assertSame(
            $this->conditionTypeRegistry->getConditionType('condition1'),
            $condition->conditionType,
        );

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $condition->id->toString());
        self::assertTrue($condition->isPublished);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $condition->ruleId->toString());
        self::assertSame(42, $condition->value);
    }

    final public function testMapRuleConditionWithInvalidConditionType(): void
    {
        $persistenceCondition = RuleCondition::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'ruleId' => 42,
                'ruleUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'unknown',
                'value' => 42,
            ],
        );

        $condition = $this->layoutResolverMapper->mapRuleCondition($persistenceCondition);

        self::assertInstanceOf(NullConditionType::class, $condition->conditionType);

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $condition->id->toString());
        self::assertTrue($condition->isPublished);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $condition->ruleId->toString());
        self::assertSame(42, $condition->value);
    }

    final public function testMapRuleGroupCondition(): void
    {
        $persistenceCondition = RuleGroupCondition::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'ruleGroupId' => 42,
                'ruleGroupUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'condition1',
                'value' => 42,
            ],
        );

        $condition = $this->layoutResolverMapper->mapRuleGroupCondition($persistenceCondition);

        self::assertSame(
            $this->conditionTypeRegistry->getConditionType('condition1'),
            $condition->conditionType,
        );

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $condition->id->toString());
        self::assertTrue($condition->isPublished);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $condition->ruleGroupId->toString());
        self::assertSame(42, $condition->value);
    }

    final public function testMapRuleGroupConditionWithInvalidConditionType(): void
    {
        $persistenceCondition = RuleGroupCondition::fromArray(
            [
                'id' => 1,
                'uuid' => '81168ed3-86f9-55ea-b153-101f96f2c136',
                'status' => PersistenceStatus::Published,
                'ruleGroupId' => 42,
                'ruleGroupUuid' => '23eece92-8cce-5155-9fef-58fb5e3decd6',
                'type' => 'unknown',
                'value' => 42,
            ],
        );

        $condition = $this->layoutResolverMapper->mapRuleGroupCondition($persistenceCondition);

        self::assertInstanceOf(NullConditionType::class, $condition->conditionType);

        self::assertSame('81168ed3-86f9-55ea-b153-101f96f2c136', $condition->id->toString());
        self::assertTrue($condition->isPublished);
        self::assertSame('23eece92-8cce-5155-9fef-58fb5e3decd6', $condition->ruleGroupId->toString());
        self::assertSame(42, $condition->value);
    }
}
