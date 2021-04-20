<?php

declare(strict_types=1);

namespace Netgen\Layouts\API\Service;

use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Condition;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroup;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCondition;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupList;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleGroupUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleList;
use Netgen\Layouts\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\Target;
use Netgen\Layouts\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\API\Values\LayoutResolver\TargetUpdateStruct;
use Ramsey\Uuid\UuidInterface;

interface LayoutResolverService extends TransactionService
{
    /**
     * Loads a rule by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule with specified UUID does not exist
     */
    public function loadRule(UuidInterface $ruleId): Rule;

    /**
     * Loads a rule draft by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule with specified UUID does not exist
     */
    public function loadRuleDraft(UuidInterface $ruleId): Rule;

    /**
     * Loads a rule archive by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule with specified UUID does not exist
     */
    public function loadRuleArchive(UuidInterface $ruleId): Rule;

    /**
     * Loads a rule group by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule group with specified UUID does not exist
     */
    public function loadRuleGroup(UuidInterface $ruleGroupId): RuleGroup;

    /**
     * Loads a rule group draft by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule group with specified UUID does not exist
     */
    public function loadRuleGroupDraft(UuidInterface $ruleGroupId): RuleGroup;

    /**
     * Loads a rule group archive by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule group with specified UUID does not exist
     */
    public function loadRuleGroupArchive(UuidInterface $ruleGroupId): RuleGroup;

    /**
     * Loads all published rules.
     *
     * If the layout is provided, only rules pointing to provided layout are returned.
     *
     * @deprecated since 1.3. Use LayoutResolverService::loadRulesFromGroup or LayoutResolverService::loadRulesForLayout
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout is not published
     */
    public function loadRules(?Layout $layout = null, int $offset = 0, ?int $limit = null): RuleList;

    /**
     * Loads all published rules pointing to the provided layout.
     *
     * Rules will be sorted by priority ascending or descending based on the $ascending flag.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout is not published
     */
    public function loadRulesForLayout(Layout $layout, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleList;

    /**
     * Returns the number of published rules.
     *
     * If the layout is provided, the count of rules pointing to provided layout is returned.
     *
     * @deprecated since 1.3. Use LayoutResolverService::getRuleCountFromGroup or LayoutResolverService::getRuleCountForLayout
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout is not published
     */
    public function getRuleCount(?Layout $layout = null): int;

    /**
     * Returns the number of published rules pointing to the provided layout.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided layout is not published
     */
    public function getRuleCountForLayout(Layout $layout): int;

    /**
     * Loads all rules from the provided parent group.
     *
     * Rules will be sorted by priority ascending or descending based on the $ascending flag.
     */
    public function loadRulesFromGroup(RuleGroup $ruleGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleList;

    /**
     * Returns the number of rules from the provided parent group.
     */
    public function getRuleCountFromGroup(RuleGroup $ruleGroup): int;

    /**
     * Loads all rule groups from the provided parent group.
     *
     * Rule groups will be sorted by priority ascending or descending based on the $ascending flag.
     */
    public function loadRuleGroups(RuleGroup $parentGroup, int $offset = 0, ?int $limit = null, bool $ascending = false): RuleGroupList;

    /**
     * Returns the number of rule groups from the provided parent group.
     */
    public function getRuleGroupCount(RuleGroup $parentGroup): int;

    /**
     * Returns all rules from the provided group that match specified target type and value.
     *
     * @param int|string $targetValue
     */
    public function matchRules(RuleGroup $ruleGroup, string $targetType, $targetValue): RuleList;

    /**
     * Loads a target by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If target with specified UUID does not exist
     */
    public function loadTarget(UuidInterface $targetId): Target;

    /**
     * Loads a target draft by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If target with specified UUID does not exist
     */
    public function loadTargetDraft(UuidInterface $targetId): Target;

    /**
     * Loads a rule condition by its' UUID.
     *
     * @deprecated since 1.3. Use LayoutResolverService::loadRuleCondition
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified UUID does not exist
     */
    public function loadCondition(UuidInterface $conditionId): RuleCondition;

    /**
     * Loads a rule condition by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified UUID does not exist
     */
    public function loadRuleCondition(UuidInterface $conditionId): RuleCondition;

    /**
     * Loads a rule condition draft by its' UUID.
     *
     * @deprecated since 1.3. Use LayoutResolverService::loadRuleConditionDraft
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified UUID does not exist
     */
    public function loadConditionDraft(UuidInterface $conditionId): RuleCondition;

    /**
     * Loads a rule condition draft by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified UUID does not exist
     */
    public function loadRuleConditionDraft(UuidInterface $conditionId): RuleCondition;

    /**
     * Loads a rule group condition by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified UUID does not exist
     */
    public function loadRuleGroupCondition(UuidInterface $conditionId): RuleGroupCondition;

    /**
     * Loads a rule group condition draft by its' UUID.
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified UUID does not exist
     */
    public function loadRuleGroupConditionDraft(UuidInterface $conditionId): RuleGroupCondition;

    /**
     * Returns if rule with provided UUID, and optionally status, exists.
     */
    public function ruleExists(UuidInterface $ruleId, ?int $status = null): bool;

    /**
     * Creates a rule.
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct, RuleGroup $targetGroup): Rule;

    /**
     * Updates a rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not a draft
     */
    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct): Rule;

    /**
     * Updates rule metadata.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not published
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
     * Creates a rule draft.
     *
     * @deprecated since 1.3. Use LayoutResolverService::createRuleDraft
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not published
     *                                                     If draft already exists for the rule and $discardExisting is set to false
     */
    public function createDraft(Rule $rule, bool $discardExisting = false): Rule;

    /**
     * Creates a rule draft.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not published
     *                                                     If draft already exists for the rule and $discardExisting is set to false
     */
    public function createRuleDraft(Rule $rule, bool $discardExisting = false): Rule;

    /**
     * Discards a rule draft.
     *
     * @deprecated since 1.3. Use LayoutResolverService::discardRuleDraft
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not a draft
     */
    public function discardDraft(Rule $rule): void;

    /**
     * Discards a rule draft.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not a draft
     */
    public function discardRuleDraft(Rule $rule): void;

    /**
     * Publishes a rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not a draft
     */
    public function publishRule(Rule $rule): Rule;

    /**
     * Restores the archived version of a rule to a draft. If draft already exists,
     * it will be removed.
     *
     * @deprecated since 1.3. Use LayoutResolverService::restoreRuleFromArchive
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided rule is not archived
     */
    public function restoreFromArchive(Rule $rule): Rule;

    /**
     * Restores the archived version of a rule to a draft. If draft already exists,
     * it will be removed.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided rule is not archived
     */
    public function restoreRuleFromArchive(Rule $rule): Rule;

    /**
     * Deletes a rule.
     */
    public function deleteRule(Rule $rule): void;

    /**
     * Returns if rule group with provided UUID, and optionally status, exists.
     */
    public function ruleGroupExists(UuidInterface $ruleGroupId, ?int $status = null): bool;

    /**
     * Creates a rule group.
     */
    public function createRuleGroup(RuleGroupCreateStruct $ruleGroupCreateStruct, ?RuleGroup $parentGroup = null): RuleGroup;

    /**
     * Updates a rule group.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule group is not a draft
     */
    public function updateRuleGroup(RuleGroup $ruleGroup, RuleGroupUpdateStruct $ruleGroupUpdateStruct): RuleGroup;

    /**
     * Updates rule group metadata.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule group is not published
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
     * Creates a rule group draft.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule group is not published
     *                                                     If draft already exists for the rule group and $discardExisting is set to false
     */
    public function createRuleGroupDraft(RuleGroup $ruleGroup, bool $discardExisting = false): RuleGroup;

    /**
     * Discards a rule group draft.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule group is not a draft
     */
    public function discardRuleGroupDraft(RuleGroup $ruleGroup): void;

    /**
     * Publishes a rule group.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule group is not a draft
     */
    public function publishRuleGroup(RuleGroup $ruleGroup): RuleGroup;

    /**
     * Restores the archived version of a rule group to a draft. If draft already exists,
     * it will be removed.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If provided rule group is not archived
     */
    public function restoreRuleGroupFromArchive(RuleGroup $ruleGroup): RuleGroup;

    /**
     * Deletes a rule group together with all groups and rules with the group.
     */
    public function deleteRuleGroup(RuleGroup $ruleGroup): void;

    /**
     * Enables a rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not published
     *                                                     If rule cannot be enabled
     */
    public function enableRule(Rule $rule): Rule;

    /**
     * Disables a rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not published
     *                                                     If rule cannot be disabled
     */
    public function disableRule(Rule $rule): Rule;

    /**
     * Enables a rule group.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule group is not published
     */
    public function enableRuleGroup(RuleGroup $ruleGroup): RuleGroup;

    /**
     * Disables a rule group.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule group is not published
     */
    public function disableRuleGroup(RuleGroup $ruleGroup): RuleGroup;

    /**
     * Adds a target to rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not a draft
     *                                                     If target of different type than it already exists in the rule is added
     */
    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct): Target;

    /**
     * Updates a target.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If target is not a draft
     */
    public function updateTarget(Target $target, TargetUpdateStruct $targetUpdateStruct): Target;

    /**
     * Removes a target.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If target is not a draft
     */
    public function deleteTarget(Target $target): void;

    /**
     * Adds a condition to rule.
     *
     * @deprecated since 1.3. Use LayoutResolverService::addRuleCondition
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not a draft
     */
    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct): RuleCondition;

    /**
     * Adds a condition to rule.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule is not a draft
     */
    public function addRuleCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct): RuleCondition;

    /**
     * Adds a condition to rule group.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If rule group is not a draft
     */
    public function addRuleGroupCondition(RuleGroup $ruleGroup, ConditionCreateStruct $conditionCreateStruct): RuleGroupCondition;

    /**
     * Updates a rule condition.
     *
     * @deprecated since 1.3. Use LayoutResolverService::updateRuleCondition
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If condition is not a draft
     */
    public function updateCondition(RuleCondition $condition, ConditionUpdateStruct $conditionUpdateStruct): RuleCondition;

    /**
     * Updates a rule condition.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If condition is not a draft
     */
    public function updateRuleCondition(RuleCondition $condition, ConditionUpdateStruct $conditionUpdateStruct): RuleCondition;

    /**
     * Updates a rule group condition.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If condition is not a draft
     */
    public function updateRuleGroupCondition(RuleGroupCondition $condition, ConditionUpdateStruct $conditionUpdateStruct): RuleGroupCondition;

    /**
     * Removes a condition.
     *
     * @throws \Netgen\Layouts\Exception\BadStateException If condition is not a draft
     */
    public function deleteCondition(Condition $condition): void;

    /**
     * Creates a new rule create struct.
     */
    public function newRuleCreateStruct(): RuleCreateStruct;

    /**
     * Creates a new rule update struct.
     */
    public function newRuleUpdateStruct(): RuleUpdateStruct;

    /**
     * Creates a new rule metadata update struct.
     */
    public function newRuleMetadataUpdateStruct(): RuleMetadataUpdateStruct;

    /**
     * Creates a new rule group create struct.
     */
    public function newRuleGroupCreateStruct(string $name): RuleGroupCreateStruct;

    /**
     * Creates a new rule group update struct.
     */
    public function newRuleGroupUpdateStruct(): RuleGroupUpdateStruct;

    /**
     * Creates a new rule group metadata update struct.
     */
    public function newRuleGroupMetadataUpdateStruct(): RuleGroupMetadataUpdateStruct;

    /**
     * Creates a new target create struct from the provided values.
     */
    public function newTargetCreateStruct(string $type): TargetCreateStruct;

    /**
     * Creates a new target update struct.
     */
    public function newTargetUpdateStruct(): TargetUpdateStruct;

    /**
     * Creates a new condition create struct from the provided values.
     */
    public function newConditionCreateStruct(string $type): ConditionCreateStruct;

    /**
     * Creates a new condition update struct.
     */
    public function newConditionUpdateStruct(): ConditionUpdateStruct;
}
