<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft;
use Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;
use Netgen\BlockManager\API\Values\TargetUpdateStruct;

interface LayoutResolverService
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
    public function loadRule($ruleId);

    /**
     * Loads a rule draft by its' ID.
     *
     * @param int|string $ruleId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function loadRuleDraft($ruleId);

    /**
     * Loads all rules.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function loadRules();

    /**
     * Returns all rules that match specified target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetType, $targetValue);

    /**
     * Loads a target by its' ID.
     *
     * @param int|string $targetId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function loadTarget($targetId);

    /**
     * Loads a target draft by its' ID.
     *
     * @param int|string $targetId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft
     */
    public function loadTargetDraft($targetId);

    /**
     * Loads a condition by its' ID.
     *
     * @param int|string $conditionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function loadCondition($conditionId);

    /**
     * Loads a condition draft by its' ID.
     *
     * @param int|string $conditionId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft
     */
    public function loadConditionDraft($conditionId);

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct);

    /**
     * Updates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function updateRule(RuleDraft $rule, RuleUpdateStruct $ruleUpdateStruct);

    /**
     * Copies a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function copyRule(Rule $rule);

    /**
     * Creates a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If draft already exists for the rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft
     */
    public function createDraft(Rule $rule);

    /**
     * Discards a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     */
    public function discardDraft(RuleDraft $rule);

    /**
     * Publishes a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function publishRule(RuleDraft $rule);

    /**
     * Deletes a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     */
    public function deleteRule(Rule $rule);

    /**
     * Enables a rule.
     *
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule cannot be enabled
     */
    public function enableRule(Rule $rule);

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule cannot be disabled
     */
    public function disableRule(Rule $rule);

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target of different type than it already exists in the rule is added
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft
     */
    public function addTarget(RuleDraft $rule, TargetCreateStruct $targetCreateStruct);

    /**
     * Updates a target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft $target
     * @param \Netgen\BlockManager\API\Values\TargetUpdateStruct $targetUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft
     */
    public function updateTarget(TargetDraft $target, TargetUpdateStruct $targetUpdateStruct);

    /**
     * Removes a target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetDraft $target
     */
    public function deleteTarget(TargetDraft $target);

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleDraft $rule
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft
     */
    public function addCondition(RuleDraft $rule, ConditionCreateStruct $conditionCreateStruct);

    /**
     * Updates a condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft $condition
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft
     */
    public function updateCondition(ConditionDraft $condition, ConditionUpdateStruct $conditionUpdateStruct);

    /**
     * Removes a condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionDraft $condition
     */
    public function deleteCondition(ConditionDraft $condition);

    /**
     * Creates a new rule create struct.
     *
     * @return \Netgen\BlockManager\API\Values\RuleCreateStruct
     */
    public function newRuleCreateStruct();

    /**
     * Creates a new rule update struct.
     *
     * @return \Netgen\BlockManager\API\Values\RuleUpdateStruct
     */
    public function newRuleUpdateStruct();

    /**
     * Creates a new target create struct.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\TargetCreateStruct
     */
    public function newTargetCreateStruct($type);

    /**
     * Creates a new target update struct.
     *
     * @return \Netgen\BlockManager\API\Values\TargetUpdateStruct
     */
    public function newTargetUpdateStruct();

    /**
     * Creates a new condition create struct.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\ConditionCreateStruct
     */
    public function newConditionCreateStruct($type);

    /**
     * Creates a new condition update struct.
     *
     * @return \Netgen\BlockManager\API\Values\ConditionUpdateStruct
     */
    public function newConditionUpdateStruct();
}
