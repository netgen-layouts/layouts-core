<?php

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

    public function loadRule($ruleId, $status)
    {
        $data = $this->queryHandler->loadRuleData($ruleId, $status);

        if (empty($data)) {
            throw new NotFoundException('rule', $ruleId);
        }

        $data = $this->mapper->mapRules($data);

        return reset($data);
    }

    public function loadRules($status, $offset = 0, $limit = null)
    {
        $data = $this->queryHandler->loadRulesData($status, $offset, $limit);

        if (empty($data)) {
            return array();
        }

        $data = $this->mapper->mapRules($data);

        return $data;
    }

    public function getRuleCount(Layout $layout)
    {
        return $this->queryHandler->getRuleCount($layout->id, Value::STATUS_PUBLISHED);
    }

    public function matchRules($targetType, $targetValue)
    {
        $data = $this->queryHandler->matchRules($targetType, $targetValue);

        if (empty($data)) {
            return array();
        }

        $data = $this->mapper->mapRules($data);

        return $data;
    }

    public function loadTarget($targetId, $status)
    {
        $data = $this->queryHandler->loadTargetData($targetId, $status);

        if (empty($data)) {
            throw new NotFoundException('target', $targetId);
        }

        $data = $this->mapper->mapTargets($data);

        return reset($data);
    }

    public function loadRuleTargets(Rule $rule)
    {
        return $this->mapper->mapTargets(
            $this->queryHandler->loadRuleTargetsData($rule)
        );
    }

    public function getTargetCount(Rule $rule)
    {
        return $this->queryHandler->getTargetCount($rule);
    }

    public function loadCondition($conditionId, $status)
    {
        $data = $this->queryHandler->loadConditionData($conditionId, $status);

        if (empty($data)) {
            throw new NotFoundException('condition', $conditionId);
        }

        $data = $this->mapper->mapConditions($data);

        return reset($data);
    }

    public function loadRuleConditions(Rule $rule)
    {
        return $this->mapper->mapConditions(
            $this->queryHandler->loadRuleConditionsData($rule)
        );
    }

    public function ruleExists($ruleId, $status)
    {
        return $this->queryHandler->ruleExists($ruleId, $status);
    }

    public function createRule(RuleCreateStruct $ruleCreateStruct)
    {
        $newRule = new Rule(
            array(
                'status' => $ruleCreateStruct->status,
                'layoutId' => $ruleCreateStruct->layoutId,
                'enabled' => $ruleCreateStruct->enabled ? true : false,
                'priority' => $ruleCreateStruct->priority !== null ?
                    (int) $ruleCreateStruct->priority :
                    0,
                'comment' => trim($ruleCreateStruct->comment),
            )
        );

        return $this->queryHandler->createRule($newRule);
    }

    public function updateRule(Rule $rule, RuleUpdateStruct $ruleUpdateStruct)
    {
        $updatedRule = clone $rule;

        if ($ruleUpdateStruct->layoutId !== null) {
            // Layout ID can be 0, to indicate removal of the linked layout
            $updatedRule->layoutId = $ruleUpdateStruct->layoutId !== 0 ?
                (int) $ruleUpdateStruct->layoutId :
                null;
        }

        if ($ruleUpdateStruct->comment !== null) {
            $updatedRule->comment = trim($ruleUpdateStruct->comment);
        }

        $this->queryHandler->updateRule($updatedRule);

        return $updatedRule;
    }

    public function updateRuleMetadata(Rule $rule, RuleMetadataUpdateStruct $ruleUpdateStruct)
    {
        $updatedRule = clone $rule;

        if ($ruleUpdateStruct->priority !== null) {
            $updatedRule->priority = (int) $ruleUpdateStruct->priority;
        }

        if ($ruleUpdateStruct->enabled !== null) {
            $updatedRule->enabled = (bool) $ruleUpdateStruct->enabled;
        }

        $this->queryHandler->updateRuleData($updatedRule);

        return $updatedRule;
    }

    public function copyRule(Rule $rule)
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

    public function createRuleStatus(Rule $rule, $newStatus)
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

    public function deleteRule($ruleId, $status = null)
    {
        $this->queryHandler->deleteRuleTargets($ruleId, $status);
        $this->queryHandler->deleteRuleConditions($ruleId, $status);
        $this->queryHandler->deleteRule($ruleId, $status);
    }

    public function addTarget(Rule $rule, TargetCreateStruct $targetCreateStruct)
    {
        $newTarget = new Target(
            array(
                'status' => $rule->status,
                'ruleId' => $rule->id,
                'type' => $targetCreateStruct->type,
                'value' => $targetCreateStruct->value,
            )
        );

        return $this->queryHandler->addTarget($newTarget);
    }

    public function updateTarget(Target $target, TargetUpdateStruct $targetUpdateStruct)
    {
        $updatedTarget = clone $target;
        $updatedTarget->value = $targetUpdateStruct->value;

        $this->queryHandler->updateTarget($updatedTarget);

        return $updatedTarget;
    }

    public function deleteTarget(Target $target)
    {
        $this->queryHandler->deleteTarget($target->id, $target->status);
    }

    public function addCondition(Rule $rule, ConditionCreateStruct $conditionCreateStruct)
    {
        $newCondition = new Condition(
            array(
                'status' => $rule->status,
                'ruleId' => $rule->id,
                'type' => $conditionCreateStruct->type,
                'value' => $conditionCreateStruct->value,
            )
        );

        return $this->queryHandler->addCondition($newCondition);
    }

    public function updateCondition(Condition $condition, ConditionUpdateStruct $conditionUpdateStruct)
    {
        $updatedCondition = clone $condition;

        $updatedCondition->value = $conditionUpdateStruct->value;

        $this->queryHandler->updateCondition($updatedCondition);

        return $updatedCondition;
    }

    public function deleteCondition(Condition $condition)
    {
        $this->queryHandler->deleteCondition($condition->id, $condition->status);
    }
}
