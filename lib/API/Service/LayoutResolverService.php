<?php

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
    public function loadRule($ruleId);

    /**
     * Loads a rule draft by its' ID.
     *
     * @param int|string $ruleId
     *
     * @throws \Netgen\BlockManager\Exception\NotFoundException If rule with specified ID does not exist
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function loadRuleDraft($ruleId);

    /**
     * Loads all published rules.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule[]
     */
    public function loadRules($offset = 0, $limit = null);

    /**
     * Returns the number of published rules pointing to provided layout.
     *
     * @param \Netgen\BlockManager\API\Values\Layout\Layout $layout
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException if provided layout is not published
     *
     * @return int
     */
    public function getRuleCount(Layout $layout);

    /**
     * Returns all rules that match specified target type and value.
     *
     * @param string $targetType
     * @param int|string $targetValue
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
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
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
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function loadConditionDraft($conditionId);

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct $ruleCreateStruct
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct);

    /**
     * Updates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct);

    /**
     * Updates rule metadata.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct $ruleUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function updateRuleMetadata(Rule $rule, RuleMetadataUpdateStruct $ruleUpdateStruct);

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
     * @param bool $discardExisting
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If draft already exists for the rule and $discardExisting is set to false
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function createDraft(Rule $rule, $discardExisting = false);

    /**
     * Discards a rule draft.
     *
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     */
    public function discardDraft(Rule $rule);

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
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     */
    public function deleteRule(Rule $rule);

    /**
     * Enables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If rule cannot be enabled
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function enableRule(Rule $rule);

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not published
     *                                                          If rule cannot be disabled
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function disableRule(Rule $rule);

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct $targetCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *                                                          If target of different type than it already exists in the rule is added
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct);

    /**
     * Updates a target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct $targetUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Target
     */
    public function updateTarget(Target $target, TargetUpdateStruct $targetUpdateStruct);

    /**
     * Removes a target.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Target $target
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If target is not a draft
     */
    public function deleteTarget(Target $target);

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct $conditionCreateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If rule is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct);

    /**
     * Updates a condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If condition is not a draft
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Condition
     */
    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct);

    /**
     * Removes a condition.
     *
     * @param \Netgen\BlockManager\API\Values\LayoutResolver\Condition $condition
     *
     * @throws \Netgen\BlockManager\Exception\BadStateException If condition is not a draft
     */
    public function deleteCondition(Condition $condition);

    /**
     * Creates a new rule create struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleCreateStruct
     */
    public function newRuleCreateStruct();

    /**
     * Creates a new rule update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleUpdateStruct
     */
    public function newRuleUpdateStruct();

    /**
     * Creates a new rule metadata update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\RuleMetadataUpdateStruct
     */
    public function newRuleMetadataUpdateStruct();

    /**
     * Creates a new target create struct from the provided values.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetCreateStruct
     */
    public function newTargetCreateStruct($type);

    /**
     * Creates a new target update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\TargetUpdateStruct
     */
    public function newTargetUpdateStruct();

    /**
     * Creates a new condition create struct from the provided values.
     *
     * @param string $type
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionCreateStruct
     */
    public function newConditionCreateStruct($type);

    /**
     * Creates a new condition update struct.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\ConditionUpdateStruct
     */
    public function newConditionUpdateStruct();
}
