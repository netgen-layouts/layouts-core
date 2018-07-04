<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandlerInterface;
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
use Netgen\BlockManager\Persistence\Values\Value;

final class LayoutResolverHandler implements LayoutResolverHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler
     */
    private $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    private $mapper;

    public function __construct(LayoutResolverQueryHandler $queryHandler, LayoutResolverMapper $mapper)
    {
        $this->queryHandler = $queryHandler;
        $this->mapper = $mapper;
    }

    public function loadRule($ruleId, int $status): Rule
    {
        $data = $this->queryHandler->loadRuleData($ruleId, $status);

        if (empty($data)) {
            throw new NotFoundException('rule', $ruleId);
        }

        return $this->mapper->mapRules($data)[0];
    }

    public function loadRules(int $status, ?Layout $layout = null, int $offset = 0, ?int $limit = null): array
    {
        $data = $this->queryHandler->loadRulesData($status, $layout, $offset, $limit);

        return $this->mapper->mapRules($data);
    }

    public function getRuleCount(?Layout $layout = null): int
    {
        return $this->queryHandler->getRuleCount(Value::STATUS_PUBLISHED, $layout);
    }

    public function matchRules(string $targetType, $targetValue): array
    {
        $data = $this->queryHandler->matchRules($targetType, $targetValue);

        if (empty($data)) {
            return [];
        }

        $data = $this->mapper->mapRules($data);

        return $data;
    }

    public function loadTarget($targetId, int $status): Target
    {
        $data = $this->queryHandler->loadTargetData($targetId, $status);

        if (empty($data)) {
            throw new NotFoundException('target', $targetId);
        }

        return $this->mapper->mapTargets($data)[0];
    }

    public function loadRuleTargets(Rule $rule): array
    {
        return $this->mapper->mapTargets(
            $this->queryHandler->loadRuleTargetsData($rule)
        );
    }

    public function getTargetCount(Rule $rule): int
    {
        return $this->queryHandler->getTargetCount($rule);
    }

    public function loadCondition($conditionId, int $status): Condition
    {
        $data = $this->queryHandler->loadConditionData($conditionId, $status);

        if (empty($data)) {
            throw new NotFoundException('condition', $conditionId);
        }

        return $this->mapper->mapConditions($data)[0];
    }

    public function loadRuleConditions(Rule $rule): array
    {
        return $this->mapper->mapConditions(
            $this->queryHandler->loadRuleConditionsData($rule)
        );
    }

    public function ruleExists($ruleId, int $status): bool
    {
        return $this->queryHandler->ruleExists($ruleId, $status);
    }

    public function createRule(RuleCreateStruct $ruleCreateStruct): Rule
    {
        $newRule = new Rule(
            [
                'status' => $ruleCreateStruct->status,
                'layoutId' => $ruleCreateStruct->layoutId,
                'enabled' => $ruleCreateStruct->enabled ? true : false,
                'priority' => $this->getRulePriority($ruleCreateStruct),
                'comment' => trim($ruleCreateStruct->comment ?? ''),
            ]
        );

        return $this->queryHandler->createRule($newRule);
    }

    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct): Rule
    {
        $updatedRule = clone $rule;

        if (is_int($ruleUpdateStruct->layoutId)) {
            // Layout ID can be 0, to indicate removal of the linked layout
            $updatedRule->layoutId = $ruleUpdateStruct->layoutId !== 0 ?
                $ruleUpdateStruct->layoutId :
                null;
        }

        if (is_string($ruleUpdateStruct->comment)) {
            $updatedRule->comment = trim($ruleUpdateStruct->comment);
        }

        $this->queryHandler->updateRule($updatedRule);

        return $updatedRule;
    }

    public function updateRuleMetadata(Rule $rule, RuleMetadataUpdateStruct $ruleUpdateStruct): Rule
    {
        $updatedRule = clone $rule;

        if (is_int($ruleUpdateStruct->priority)) {
            $updatedRule->priority = $ruleUpdateStruct->priority;
        }

        if (is_bool($ruleUpdateStruct->enabled)) {
            $updatedRule->enabled = $ruleUpdateStruct->enabled;
        }

        $this->queryHandler->updateRuleData($updatedRule);

        return $updatedRule;
    }

    public function copyRule(Rule $rule): Rule
    {
        // First copy the rule

        $copiedRule = clone $rule;
        $copiedRule->id = null;

        $copiedRule = $this->queryHandler->createRule($copiedRule);

        // Then copy rule targets

        $ruleTargets = $this->loadRuleTargets($rule);

        foreach ($ruleTargets as $ruleTarget) {
            $copiedTarget = clone $ruleTarget;

            $copiedTarget->id = null;
            $copiedTarget->ruleId = $copiedRule->id;

            $this->queryHandler->addTarget($copiedTarget);
        }

        // Then copy rule conditions

        $ruleConditions = $this->loadRuleConditions($rule);

        foreach ($ruleConditions as $ruleCondition) {
            $copiedCondition = clone $ruleCondition;

            $copiedCondition->id = null;
            $copiedCondition->ruleId = $copiedRule->id;

            $this->queryHandler->addCondition($copiedCondition);
        }

        return $copiedRule;
    }

    public function createRuleStatus(Rule $rule, int $newStatus): Rule
    {
        // First copy the rule

        $copiedRule = clone $rule;
        $copiedRule->status = $newStatus;

        $copiedRule = $this->queryHandler->createRule($copiedRule);

        // Then copy rule targets

        $ruleTargets = $this->loadRuleTargets($rule);

        foreach ($ruleTargets as $ruleTarget) {
            $copiedTarget = clone $ruleTarget;
            $copiedTarget->status = $newStatus;

            $this->queryHandler->addTarget($copiedTarget);
        }

        // Then copy rule conditions

        $ruleConditions = $this->loadRuleConditions($rule);

        foreach ($ruleConditions as $ruleCondition) {
            $copiedCondition = clone $ruleCondition;
            $copiedCondition->status = $newStatus;

            $this->queryHandler->addCondition($copiedCondition);
        }

        return $copiedRule;
    }

    public function deleteRule($ruleId, ?int $status = null): void
    {
        $this->queryHandler->deleteRuleTargets($ruleId, $status);
        $this->queryHandler->deleteRuleConditions($ruleId, $status);
        $this->queryHandler->deleteRule($ruleId, $status);
    }

    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct): Target
    {
        $newTarget = new Target(
            [
                'status' => $rule->status,
                'ruleId' => $rule->id,
                'type' => $targetCreateStruct->type,
                'value' => $targetCreateStruct->value,
            ]
        );

        return $this->queryHandler->addTarget($newTarget);
    }

    public function updateTarget(Target $target, TargetUpdateStruct $targetUpdateStruct): Target
    {
        $updatedTarget = clone $target;
        $updatedTarget->value = $targetUpdateStruct->value;

        $this->queryHandler->updateTarget($updatedTarget);

        return $updatedTarget;
    }

    public function deleteTarget(Target $target): void
    {
        $this->queryHandler->deleteTarget($target->id, $target->status);
    }

    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct): Condition
    {
        $newCondition = new Condition(
            [
                'status' => $rule->status,
                'ruleId' => $rule->id,
                'type' => $conditionCreateStruct->type,
                'value' => $conditionCreateStruct->value,
            ]
        );

        return $this->queryHandler->addCondition($newCondition);
    }

    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct): Condition
    {
        $updatedCondition = clone $condition;

        $updatedCondition->value = $conditionUpdateStruct->value;

        $this->queryHandler->updateCondition($updatedCondition);

        return $updatedCondition;
    }

    public function deleteCondition(Condition $condition): void
    {
        $this->queryHandler->deleteCondition($condition->id, $condition->status);
    }

    /**
     * Returns the rule priority when creating a new rule.
     *
     * If priority is specified in the struct, it is used automatically. Otherwise,
     * the returned priority is the lowest available priority subtracted by 10 (to allow
     * inserting rules in between).
     *
     * If no rules exist, priority is 0.
     */
    private function getRulePriority(RuleCreateStruct $ruleCreateStruct): int
    {
        if (is_int($ruleCreateStruct->priority)) {
            return $ruleCreateStruct->priority;
        }

        $lowestRulePriority = $this->queryHandler->getLowestRulePriority();
        if ($lowestRulePriority !== null) {
            return $lowestRulePriority - 10;
        }

        return 0;
    }
}
