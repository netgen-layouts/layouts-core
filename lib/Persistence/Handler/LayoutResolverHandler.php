<?php

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\API\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct;

interface LayoutResolverHandler
{
    /**
     * Loads a rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function loadRule($ruleId, $status);

    /**
     * Loads all rules.
     *
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function loadRules($status);

    /**
     * Returns all rules that match specified target identifier and value.
     *
     * @param string $targetIdentifier
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetIdentifier, $targetValue);

    /**
     * Loads an target with specified ID.
     *
     * @param int|string $targetId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If target with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function loadTarget($targetId, $status);

    /**
     * Loads all targets that belong to rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target[]
     */
    public function loadRuleTargets($ruleId, $status);

    /**
     * Loads the count of targets within the rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return int
     */
    public function getTargetCount($ruleId, $status);

    /**
     * Loads a condition with specified ID.
     *
     * @param int|string $conditionId
     * @param int $status
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If condition with specified ID does not exist
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function loadCondition($conditionId, $status);

    /**
     * Loads all conditions that belong to rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition[]
     */
    public function loadRuleConditions($ruleId, $status);

    /**
     * Returns if rule with specified ID exists.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return bool
     */
    public function ruleExists($ruleId, $status);

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct, $status);

    /**
     * Updates a rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function updateRule($ruleId, $status, RuleUpdateStruct $ruleUpdateStruct);

    /**
     * Copies a rule with specified ID.
     *
     * @param int|string $ruleId
     *
     * @return int The ID of copied rule
     */
    public function copyRule($ruleId);

    /**
     * Creates a new rule status.
     *
     * @param int|string $ruleId
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRuleStatus($ruleId, $status, $newStatus);

    /**
     * Deletes a rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     */
    public function deleteRule($ruleId, $status = null);

    /**
     * Enables a rule.
     *
     * @param int|string $ruleId
     */
    public function enableRule($ruleId);

    /**
     * Disables a rule.
     *
     * @param int|string $ruleId
     */
    public function disableRule($ruleId);

    /**
     * Adds a target to rule.
     *
     * @param int|string $ruleId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function addTarget($ruleId, $status, TargetCreateStruct $targetCreateStruct);

    /**
     * Removes a target.
     *
     * @param int|string $targetId
     * @param int $status
     */
    public function deleteTarget($targetId, $status);

    /**
     * Adds a condition to rule.
     *
     * @param int|string $ruleId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function addCondition($ruleId, $status, ConditionCreateStruct $conditionCreateStruct);

    /**
     * Updates a condition with specified ID.
     *
     * @param int|string $conditionId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function updateCondition($conditionId, $status, ConditionUpdateStruct $conditionUpdateStruct);

    /**
     * Removes a condition.
     *
     * @param int|string $conditionId
     * @param int $status
     */
    public function deleteCondition($conditionId, $status);
}
