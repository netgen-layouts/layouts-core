<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LazyCollection;
use Netgen\Layouts\Exception\Layout\ConditionTypeException;
use Netgen\Layouts\Exception\Layout\TargetTypeException;
use Netgen\Layouts\Exception\NotFoundException;
use Netgen\Layouts\Layout\Resolver\ConditionType\NullConditionType;
use Netgen\Layouts\Layout\Resolver\Registry\ConditionTypeRegistry;
use Netgen\Layouts\Layout\Resolver\Registry\TargetTypeRegistry;
use Netgen\Layouts\Layout\Resolver\TargetType\NullTargetType;
use Netgen\Layouts\Persistence\Handler\LayoutResolverHandlerInterface;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule as PersistenceRule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition as PersistenceRuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup as PersistenceRuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition as PersistenceRuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target as PersistenceTarget;
use Netgen\Layouts\Persistence\Values\Value;
use Ramsey\Uuid\Uuid;

use function array_map;

final class LayoutResolverMapper
{
    private LayoutResolverHandlerInterface $layoutResolverHandler;

    private TargetTypeRegistry $targetTypeRegistry;

    private ConditionTypeRegistry $conditionTypeRegistry;

    private LayoutService $layoutService;

    public function __construct(
        LayoutResolverHandlerInterface $layoutResolverHandler,
        TargetTypeRegistry $targetTypeRegistry,
        ConditionTypeRegistry $conditionTypeRegistry,
        LayoutService $layoutService
    ) {
        $this->layoutResolverHandler = $layoutResolverHandler;
        $this->targetTypeRegistry = $targetTypeRegistry;
        $this->conditionTypeRegistry = $conditionTypeRegistry;
        $this->layoutService = $layoutService;
    }

    /**
     * Builds the API rule value from persistence one.
     */
    public function mapRule(PersistenceRule $rule): Rule
    {
        $ruleData = [
            'id' => Uuid::fromString($rule->uuid),
            'status' => $rule->status,
            'ruleGroupId' => Uuid::fromString(
                $this->layoutResolverHandler->loadRuleGroup(
                    $rule->ruleGroupId,
                    Value::STATUS_PUBLISHED,
                )->uuid,
            ),
            'layout' => function () use ($rule): ?Layout {
                try {
                    // Layouts used by rule are always in published status
                    return $rule->layoutUuid !== null ?
                        $this->layoutService->loadLayout(Uuid::fromString($rule->layoutUuid)) :
                        null;
                } catch (NotFoundException $e) {
                    return null;
                }
            },
            'enabled' => $rule->enabled,
            'priority' => $rule->priority,
            'description' => $rule->description,
            'targets' => new LazyCollection(
                fn (): array => array_map(
                    fn (PersistenceTarget $target): Target => $this->mapTarget($target),
                    $this->layoutResolverHandler->loadRuleTargets($rule),
                ),
            ),
            'conditions' => new LazyCollection(
                fn (): array => array_map(
                    fn (PersistenceRuleCondition $condition): RuleCondition => $this->mapRuleCondition($condition),
                    $this->layoutResolverHandler->loadRuleConditions($rule),
                ),
            ),
        ];

        return Rule::fromArray($ruleData);
    }

    /**
     * Builds the API rule group value from persistence one.
     */
    public function mapRuleGroup(PersistenceRuleGroup $ruleGroup): RuleGroup
    {
        $ruleGroupData = [
            'id' => Uuid::fromString($ruleGroup->uuid),
            'parentId' => $ruleGroup->parentUuid !== null ?
                Uuid::fromString($ruleGroup->parentUuid) :
                null,
            'status' => $ruleGroup->status,
            'name' => $ruleGroup->name,
            'description' => $ruleGroup->description,
            'enabled' => $ruleGroup->enabled,
            'priority' => $ruleGroup->priority,
            'rules' => new LazyCollection(
                fn (): array => array_map(
                    fn (PersistenceRule $rule): Rule => $this->mapRule($rule),
                    $this->layoutResolverHandler->loadRulesFromGroup($ruleGroup),
                ),
            ),
            'conditions' => new LazyCollection(
                fn (): array => array_map(
                    fn (PersistenceRuleGroupCondition $condition): RuleGroupCondition => $this->mapRuleGroupCondition($condition),
                    $this->layoutResolverHandler->loadRuleGroupConditions($ruleGroup),
                ),
            ),
        ];

        return RuleGroup::fromArray($ruleGroupData);
    }

    /**
     * Builds the API target value from persistence one.
     */
    public function mapTarget(PersistenceTarget $target): Target
    {
        try {
            $targetType = $this->targetTypeRegistry->getTargetType(
                $target->type,
            );
        } catch (TargetTypeException $e) {
            $targetType = new NullTargetType();
        }

        $targetData = [
            'id' => Uuid::fromString($target->uuid),
            'status' => $target->status,
            'ruleId' => Uuid::fromString($target->ruleUuid),
            'targetType' => $targetType,
            'value' => $target->value,
        ];

        return Target::fromArray($targetData);
    }

    /**
     * Builds the API rule condition value from persistence one.
     */
    public function mapRuleCondition(PersistenceRuleCondition $condition): RuleCondition
    {
        try {
            $conditionType = $this->conditionTypeRegistry->getConditionType(
                $condition->type,
            );
        } catch (ConditionTypeException $e) {
            $conditionType = new NullConditionType();
        }

        $conditionData = [
            'id' => Uuid::fromString($condition->uuid),
            'status' => $condition->status,
            'ruleId' => Uuid::fromString($condition->ruleUuid),
            'conditionType' => $conditionType,
            'value' => $condition->value,
        ];

        return RuleCondition::fromArray($conditionData);
    }

    /**
     * Builds the API rule group condition value from persistence one.
     */
    public function mapRuleGroupCondition(PersistenceRuleGroupCondition $condition): RuleGroupCondition
    {
        try {
            $conditionType = $this->conditionTypeRegistry->getConditionType(
                $condition->type,
            );
        } catch (ConditionTypeException $e) {
            $conditionType = new NullConditionType();
        }

        $conditionData = [
            'id' => Uuid::fromString($condition->uuid),
            'status' => $condition->status,
            'ruleGroupId' => Uuid::fromString($condition->ruleGroupUuid),
            'conditionType' => $conditionType,
            'value' => $condition->value,
        ];

        return RuleGroupCondition::fromArray($conditionData);
    }
}
