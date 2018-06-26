<?php

declare(strict_types=1);

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct;

interface LayoutResolverService extends Service
{
    /**
     * Loads a rule by its' ID.
     *
     * @param int|string $ruleId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function loadRule($ruleId): Rule;

    /**
     * Loads a rule draft by its' ID.
     *
     * @param int|string $ruleId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function loadRuleDraft($ruleId): Rule;

    /**
     * Loads a rule archive by its' ID.
     *
     * @param int|string $ruleId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function loadRuleArchive($ruleId): Rule;

    /**
     * Loads all published rules.
     *
     * If the layout is provided, only rules pointing to provided layout are returned.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException if provided layout is not published
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function loadRules(?Layout $layout = null, int $offset = 0, ?int $limit = null): array;

    /**
     * Returns the number of published rules.
     *
     * If the layout is provided, the count of rules pointing to provided layout is returned.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException if provided layout is not published
     */
    public function getRuleCount(?Layout $layout = null): int;

    /**
     * Returns all rules that match specified target type and value.
     *
     * @param string $targetType
     * @param int|string $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules(string $targetType, $targetValue): array;

    /**
     * Loads a target by its' ID.
     *
     * @param int|string $targetId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function loadTarget($targetId): Target;

    /**
     * Loads a target draft by its' ID.
     *
     * @param int|string $targetId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function loadTargetDraft($targetId): Target;

    /**
     * Loads a condition by its' ID.
     *
     * @param int|string $conditionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function loadCondition($conditionId): Condition;

    /**
     * Loads a condition draft by its' ID.
     *
     * @param int|string $conditionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function loadConditionDraft($conditionId): Condition;

    /**
     * Creates a rule.
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct): Rule;

    /**
     * Updates a rule.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     */
    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct): Rule;

    /**
     * Updates rule metadata.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     */
    public function updateRuleMetadata(Rule $rule, RuleMetadataUpdateStruct $ruleUpdateStruct): Rule;

    /**
     * Copies a rule.
     */
    public function copyRule(Rule $rule): Rule;

    /**
     * Creates a rule draft.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If draft already exists for the rule and $discardExisting is set to false
     */
    public function createDraft(Rule $rule, bool $discardExisting = false): Rule;

    /**
     * Discards a rule draft.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     */
    public function discardDraft(Rule $rule): void;

    /**
     * Publishes a rule.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     */
    public function publishRule(Rule $rule): Rule;

    /**
     * Restores the archived version of a rule to a draft. If draft already exists,
     * it will be removed.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If provided rule is not archived
     */
    public function restoreFromArchive(Rule $rule): Rule;

    /**
     * Deletes a rule.
     */
    public function deleteRule(Rule $rule): void;

    /**
     * Enables a rule.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If rule cannot be enabled
     */
    public function enableRule(Rule $rule): Rule;

    /**
     * Disables a rule.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If rule cannot be disabled
     */
    public function disableRule(Rule $rule): Rule;

    /**
     * Adds a target to rule.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *                                                          If target of different type than it already exists in the rule is added
     */
    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct): Target;

    /**
     * Updates a target.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target is not a draft
     */
    public function updateTarget(Target $target, TargetUpdateStruct $targetUpdateStruct): Target;

    /**
     * Removes a target.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target is not a draft
     */
    public function deleteTarget(Target $target): void;

    /**
     * Adds a condition to rule.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     */
    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct): Condition;

    /**
     * Updates a condition.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If condition is not a draft
     */
    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct): Condition;

    /**
     * Removes a condition.
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If condition is not a draft
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
