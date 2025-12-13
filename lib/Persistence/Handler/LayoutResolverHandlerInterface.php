<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Handler;

use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetUpdateStruct;
use Netgen\Layouts\Persistence\Values\Status;
use Symfony\Component\Uid\Uuid;

interface LayoutResolverHandlerInterface
{
    /**
     * Loads a rule with specified ID.
     *
     * Rule ID can be an auto-incremented ID or an UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule with specified ID does not exist
     */
    public function loadRule(int|string|Uuid $ruleId, Status $status): Rule;

    /**
     * Loads a rule group with specified ID.
     *
     * Rule group ID can be an auto-incremented ID or an UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule group with specified ID does not exist
     */
    public function loadRuleGroup(int|string|Uuid $ruleGroupId, Status $status): RuleGroup;

    /**
     * Loads all rules mapped to provided layout.
     *
     * Rules will be sorted by priority ascending or descending based on the $ascending flag.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Rule[]
     */
    public function loadRulesForLayout(Layout $layout, int $offset = 0, ?int $limit = null, bool $ascending = false): array;

    /**
     * Returns the number of published rules mapped to provided layout.
     *
     * @return int<0, max>
     */
    public function getRuleCountForLayout(Layout $layout): int;

    /**
     * Loads all rules from the provided parent group.
     *
     * Rules will be sorted by priority ascending or descending based on the $ascending flag.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Rule[]
     */
    public function loadRulesFromGroup(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): array;

    /**
     * Returns the number of rules from the provided parent group.
     *
     * @return int<0, max>
     */
    public function getRuleCountFromGroup(RuleGroup $ruleGroup): int;

    /**
     * Loads all rule groups from the provided parent group.
     *
     * Rule groups will be sorted by priority ascending or descending based on the $ascending flag.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroup[]
     */
    public function loadRuleGroups(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): array;

    /**
     * Returns the number of rule groups from the provided parent group.
     *
     * @return int<0, max>
     */
    public function getRuleGroupCount(RuleGroup $ruleGroup): int;

    /**
     * Returns all rules from the provided group that match specified target type and value.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Rule[]
     */
    public function matchRules(RuleGroup $ruleGroup, string $targetType, mixed $targetValue): array;

    /**
     * Loads an target with specified ID.
     *
     * Target ID can be an auto-incremented ID or an UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If target with specified ID does not exist
     */
    public function loadTarget(int|string|Uuid $targetId, Status $status): Target;

    /**
     * Loads all targets that belong to rule with specified ID.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Target[]
     */
    public function loadRuleTargets(Rule $rule): array;

    /**
     * Loads a rule condition with specified ID.
     *
     * Condition ID can be an auto-incremented ID or an UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified ID does not exist
     */
    public function loadRuleCondition(int|string|Uuid $conditionId, Status $status): RuleCondition;

    /**
     * Loads a rule group condition with specified ID.
     *
     * Condition ID can be an auto-incremented ID or an UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified ID does not exist
     */
    public function loadRuleGroupCondition(int|string|Uuid $conditionId, Status $status): RuleGroupCondition;

    /**
     * Loads all conditions that belong to rule with specified ID.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCondition[]
     */
    public function loadRuleConditions(Rule $rule): array;

    /**
     * Loads all conditions that belong to rule group with specified ID.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\RuleGroupCondition[]
     */
    public function loadRuleGroupConditions(RuleGroup $ruleGroup): array;

    /**
     * Returns if rule with specified ID exists.
     *
     * Rule ID can be an auto-incremented ID or an UUID.
     */
    public function ruleExists(int|string|Uuid $ruleId, ?Status $status = null): bool;

    /**
     * Creates a rule.
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct, RuleGroup $targetGroup): Rule;

    /**
     * Updates a rule with specified ID.
     */
    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct): Rule;

    /**
     * Updates rule metadata.
     */
    public function updateRuleMetadata(Rule $rule, RuleMetadataUpdateStruct $ruleUpdateStruct): Rule;

    /**
     * Copies a rule.
     */
    public function copyRule(Rule $rule, RuleGroup $targetGroup): Rule;

    /**
     * Moves a rule.
     */
    public function moveRule(Rule $rule, RuleGroup $targetGroup, ?int $newPriority = null): Rule;

    /**
     * Creates a new rule status.
     */
    public function createRuleStatus(Rule $rule, Status $newStatus): Rule;

    /**
     * Deletes a rule with specified ID.
     */
    public function deleteRule(int $ruleId, ?Status $status = null): void;

    /**
     * Returns if rule group with specified ID exists.
     *
     * Rule group ID can be an auto-incremented ID or an UUID.
     */
    public function ruleGroupExists(int|string|Uuid $ruleGroupId, ?Status $status = null): bool;

    /**
     * Creates a rule group.
     */
    public function createRuleGroup(RuleGroupCreateStruct $ruleGroupCreateStruct, ?RuleGroup $parentGroup = null): RuleGroup;

    /**
     * Updates a rule group with specified ID.
     */
    public function updateRuleGroup(RuleGroup $ruleGroup, RuleGroupUpdateStruct $ruleGroupUpdateStruct): RuleGroup;

    /**
     * Updates rule group metadata.
     */
    public function updateRuleGroupMetadata(RuleGroup $ruleGroup, RuleGroupMetadataUpdateStruct $ruleGroupUpdateStruct): RuleGroup;

    /**
     * Copies a rule group.
     *
     * If $copyChildren is set to true, all groups and rules within the group will also be copied.
     */
    public function copyRuleGroup(RuleGroup $ruleGroup, RuleGroup $targetGroup, bool $copyChildren = false): RuleGroup;

    /**
     * Moves a rule group.
     */
    public function moveRuleGroup(RuleGroup $ruleGroup, RuleGroup $targetGroup, ?int $newPriority = null): RuleGroup;

    /**
     * Creates a new rule group status.
     */
    public function createRuleGroupStatus(RuleGroup $ruleGroup, Status $newStatus): RuleGroup;

    /**
     * Deletes a rule group with specified ID.
     */
    public function deleteRuleGroup(int $ruleGroupId, ?Status $status = null): void;

    /**
     * Adds a target to rule.
     */
    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct): Target;

    /**
     * Updates a target with specified ID.
     */
    public function updateTarget(Target $target, TargetUpdateStruct $targetUpdateStruct): Target;

    /**
     * Removes a target.
     */
    public function deleteTarget(Target $target): void;

    /**
     * Adds a condition to rule.
     */
    public function addRuleCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct): RuleCondition;

    /**
     * Adds a condition to rule group.
     */
    public function addRuleGroupCondition(RuleGroup $ruleGroup, ConditionCreateStruct $conditionCreateStruct): RuleGroupCondition;

    /**
     * Updates a condition with specified ID.
     */
    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct): Condition;

    /**
     * Removes a condition.
     */
    public function deleteCondition(Condition $condition): void;
}
