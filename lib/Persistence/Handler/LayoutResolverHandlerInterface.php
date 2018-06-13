<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Handler;

use Netgen\BlockManager\Persistence\Values\Layout\Layout;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetUpdateStruct;

interface LayoutResolverHandlerInterface
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
     * If the layout is provided, only rules pointing to provided layout are returned.
     *
     * @param int $status
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function loadRules($status, Layout $layout = null, $offset = 0, $limit = null);

    /**
     * Returns the number of published rules.
     *
     * If the layout is provided, the count of rules pointing to provided layout is returned.
     *
     * @param \Netgen\BlockManager\Persistence\Values\Layout\Layout $layout
     *
     * @return int
     */
    public function getRuleCount(Layout $layout = null);

    /**
     * Returns all rules that match specified target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetType, $targetValue);

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
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target[]
     */
    public function loadRuleTargets(Rule $rule);

    /**
     * Loads the count of targets within the rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return int
     */
    public function getTargetCount(Rule $rule);

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
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition[]
     */
    public function loadRuleConditions(Rule $rule);

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
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleCreateStruct $ruleCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct);

    /**
     * Updates a rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct);

    /**
     * Updates rule metadata.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function updateRuleMetadata(Rule $rule, RuleMetadataUpdateStruct $ruleUpdateStruct);

    /**
     * Copies a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function copyRule(Rule $rule);

    /**
     * Creates a new rule status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRuleStatus(Rule $rule, $newStatus);

    /**
     * Deletes a rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     */
    public function deleteRule($ruleId, $status = null);

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetCreateStruct $targetCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct);

    /**
     * Updates a target with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\TargetUpdateStruct $targetUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function updateTarget(Target $target, TargetUpdateStruct $targetUpdateStruct);

    /**
     * Removes a target.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     */
    public function deleteTarget(Target $target);

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct);

    /**
     * Updates a condition with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct);

    /**
     * Removes a condition.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     */
    public function deleteCondition(Condition $condition);
}
