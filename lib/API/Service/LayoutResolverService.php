<?php

namespace Netgen\BlockManager\API\Service;

use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\LayoutResolver\Condition;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\API\Values\LayoutResolver\Target;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;

interface LayoutResolverService
{
    /**
     * Loads a rule by its' ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function loadRule($ruleId, $status = Rule::STATUS_PUBLISHED);

    /**
     * Loads all rules.
     *
     * @param int $status
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function loadRules($status = Rule::STATUS_PUBLISHED);

    /**
     * Returns all rules that match specified target identifier and value.
     *
     * @param string $targetIdentifier
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetIdentifier, $targetValue);

    /**
     * Loads a target by its' ID.
     *
     * @param int|string $targetId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function loadTarget($targetId, $status = Rule::STATUS_PUBLISHED);

    /**
     * Loads a condition by its' ID.
     *
     * @param int|string $conditionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function loadCondition($conditionId, $status = Rule::STATUS_PUBLISHED);

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct);

    /**
     * Updates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct);

    /**
     * Copies a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function copyRule(Rule $rule);

    /**
     * Creates a new rule status.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule already has the provided status
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createRuleStatus(Rule $rule, $status);

    /**
     * Creates a rule draft.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If draft already exists for the rule
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createDraft(Rule $rule);

    /**
     * Publishes a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function publishRule(Rule $rule);

    /**
     * Deletes a rule.
     *
     * @param bool $deleteAllStatuses
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     */
    public function deleteRule(Rule $rule, $deleteAllStatuses = false);

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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target of different type than it already exists in the rule is added
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct);

    /**
     * Removes a target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     */
    public function deleteTarget(Target $target);

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct);

    /**
     * Updates a condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct);

    /**
     * Removes a condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     */
    public function deleteCondition(Condition $condition);

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
     * @param string $identifier
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\API\Values\TargetCreateStruct
     */
    public function newTargetCreateStruct($identifier, $value);

    /**
     * Creates a new condition create struct.
     *
     * @param string $identifier
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\API\Values\ConditionCreateStruct
     */
    public function newConditionCreateStruct($identifier, $value);

    /**
     * Creates a new condition update struct.
     *
     * @param mixed $value
     *
     * @return \Netgen\BlockManager\API\Values\ConditionUpdateStruct
     */
    public function newConditionUpdateStruct($value);
}
