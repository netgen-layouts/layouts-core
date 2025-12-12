<?php

declare(strict_types=1);

namespace Netgen\Layouts\Core\Mapper;

use Netgen\Layouts\API\Service\LayoutService;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionList;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetList;
use Netgen\Layouts\API\Values\Status;
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
use Netgen\Layouts\Persistence\Values\Status as PersistenceStatus;
use Ramsey\Uuid\Uuid;

use function array_map;

final class LayoutResolverMapper
{
    public function __construct(
        private LayoutResolverHandlerInterface $layoutResolverHandler,
        private TargetTypeRegistry $targetTypeRegistry,
        private ConditionTypeRegistry $conditionTypeRegistry,
        private LayoutService $layoutService,
    ) {}

    /**
     * Builds the API rule value from persistence one.
     */
    public function mapRule(PersistenceRule $rule): Rule
    {
        $ruleData = [
            'id' => Uuid::fromString($rule->uuid),
            'status' => Status::from($rule->status->value),
            'ruleGroupId' => Uuid::fromString(
                $this->layoutResolverHandler->loadRuleGroup(
                    $rule->ruleGroupId,
                    PersistenceStatus::Published,
                )->uuid,
            ),
            'isEnabled' => $rule->isEnabled,
            'priority' => $rule->priority,
            'description' => $rule->description,
            'targets' => TargetList::fromCallable(
                fn (): array => array_map(
                    $this->mapTarget(...),
                    $this->layoutResolverHandler->loadRuleTargets($rule),
                ),
            ),
            'conditions' => ConditionList::fromCallable(
                fn (): array => array_map(
                    $this->mapRuleCondition(...),
                    $this->layoutResolverHandler->loadRuleConditions($rule),
                ),
            ),
        ];

        return Rule::fromArray(
            $ruleData,
            [
                'layout' => function () use ($rule): ?Layout {
                    try {
                        // Layouts used by rule are always in published status
                        return $rule->layoutUuid !== null ?
                            $this->layoutService->loadLayout(Uuid::fromString($rule->layoutUuid)) :
                            null;
                    } catch (NotFoundException) {
                        return null;
                    }
                },
            ],
        );
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
            'status' => Status::from($ruleGroup->status->value),
            'name' => $ruleGroup->name,
            'description' => $ruleGroup->description,
            'isEnabled' => $ruleGroup->isEnabled,
            'priority' => $ruleGroup->priority,
            'rules' => RuleList::fromCallable(
                fn (): array => array_map(
                    $this->mapRule(...),
                    $this->layoutResolverHandler->loadRulesFromGroup($ruleGroup),
                ),
            ),
            'conditions' => ConditionList::fromCallable(
                fn (): array => array_map(
                    $this->mapRuleGroupCondition(...),
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
        } catch (TargetTypeException) {
            $targetType = new NullTargetType();
        }

        $targetData = [
            'id' => Uuid::fromString($target->uuid),
            'status' => Status::from($target->status->value),
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
        } catch (ConditionTypeException) {
            $conditionType = new NullConditionType();
        }

        $conditionData = [
            'id' => Uuid::fromString($condition->uuid),
            'status' => Status::from($condition->status->value),
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
        } catch (ConditionTypeException) {
            $conditionType = new NullConditionType();
        }

        $conditionData = [
            'id' => Uuid::fromString($condition->uuid),
            'status' => Status::from($condition->status->value),
            'ruleGroupId' => Uuid::fromString($condition->ruleGroupUuid),
            'conditionType' => $conditionType,
            'value' => $condition->value,
        ];

        return RuleGroupCondition::fromArray($conditionData);
    }
}
