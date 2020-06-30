<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Handler;

use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\ConditionUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleMetadataUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\RuleUpdateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetCreateStruct;
use Netgen\Layouts\Persistence\Values\LayoutResolver\TargetUpdateStruct;

interface LayoutResolverHandlerInterface
{
    /**
     * Loads a rule with specified ID.
     *
     * Rule ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $ruleId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If rule with specified ID does not exist
     */
    public function loadRule($ruleId, int $status): Rule;

    /**
     * Loads all rules.
     *
     * If the layout is provided, only rules pointing to provided layout are returned.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Rule[]
     */
    public function loadRules(int $status, ?Layout $layout = null, int $offset = 0, ?int $limit = null): array;

    /**
     * Returns the number of published rules.
     *
     * If the layout is provided, the count of rules pointing to provided layout is returned.
     */
    public function getRuleCount(?Layout $layout = null): int;

    /**
     * Returns all rules that match specified target type and value.
     *
     * @param mixed $targetValue
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Rule[]
     */
    public function matchRules(string $targetType, $targetValue): array;

    /**
     * Loads an target with specified ID.
     *
     * Target ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $targetId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If target with specified ID does not exist
     */
    public function loadTarget($targetId, int $status): Target;

    /**
     * Loads all targets that belong to rule with specified ID.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Target[]
     */
    public function loadRuleTargets(Rule $rule): array;

    /**
     * Loads the count of targets within the rule with specified ID.
     */
    public function getTargetCount(Rule $rule): int;

    /**
     * Loads a condition with specified ID.
     *
     * Condition ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $conditionId
     *
     * @throws \Netgen\Layouts\Exception\NotFoundException If condition with specified ID does not exist
     */
    public function loadCondition($conditionId, int $status): Condition;

    /**
     * Loads all conditions that belong to rule with specified ID.
     *
     * @return \Netgen\Layouts\Persistence\Values\LayoutResolver\Condition[]
     */
    public function loadRuleConditions(Rule $rule): array;

    /**
     * Returns if rule with specified ID exists.
     *
     * Rule ID can be an auto-incremented ID or an UUID.
     *
     * @param int|string|\Ramsey\Uuid\UuidInterface $ruleId
     */
    public function ruleExists($ruleId, ?int $status = null): bool;

    /**
     * Creates a rule.
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct): Rule;

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
    public function copyRule(Rule $rule): Rule;

    /**
     * Creates a new rule status.
     */
    public function createRuleStatus(Rule $rule, int $newStatus): Rule;

    /**
     * Deletes a rule with specified ID.
     */
    public function deleteRule(int $ruleId, ?int $status = null): void;

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
    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct): Condition;

    /**
     * Updates a condition with specified ID.
     */
    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct): Condition;

    /**
     * Removes a condition.
     */
    public function deleteCondition(Condition $condition): void;
}
