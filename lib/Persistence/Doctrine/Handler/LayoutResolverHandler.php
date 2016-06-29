<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\ConditionCreateStruct as APIConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct as APIConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\RuleCreateStruct as APIRuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct as APIRuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct as APITargetCreateStruct;
use Netgen\BlockManager\API\Values\TargetUpdateStruct as APITargetUpdateStruct;
use Netgen\BlockManager\Persistence\Values\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler as LayoutResolverHandlerInterface;
use Netgen\BlockManager\Exception\NotFoundException;
use Netgen\BlockManager\Persistence\Values\TargetUpdateStruct;

class LayoutResolverHandler implements LayoutResolverHandlerInterface
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler
     */
    protected $queryHandler;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper
     */
    protected $mapper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler $queryHandler
     * @param \Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper $mapper
     */
    public function __construct(LayoutResolverQueryHandler $queryHandler, LayoutResolverMapper $mapper)
    {
        $this->queryHandler = $queryHandler;
        $this->mapper = $mapper;
    }

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
    public function loadRule($ruleId, $status)
    {
        $data = $this->queryHandler->loadRuleData($ruleId, $status);

        if (empty($data)) {
            throw new NotFoundException('rule', $ruleId);
        }

        $data = $this->mapper->mapRules($data);

        return reset($data);
    }

    /**
     * Loads all rules.
     *
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function loadRules($status)
    {
        $data = $this->queryHandler->loadRulesData($status);

        if (empty($data)) {
            return array();
        }

        $data = $this->mapper->mapRules($data);

        return $data;
    }

    /**
     * Returns all rules that match specified target identifier and value.
     *
     * @param string $targetIdentifier
     * @param mixed $targetValue
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule[]
     */
    public function matchRules($targetIdentifier, $targetValue)
    {
        $data = $this->queryHandler->matchRules($targetIdentifier, $targetValue);

        if (empty($data)) {
            return array();
        }

        $data = $this->mapper->mapRules($data);

        return $data;
    }

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
    public function loadTarget($targetId, $status)
    {
        $data = $this->queryHandler->loadTargetData($targetId, $status);

        if (empty($data)) {
            throw new NotFoundException('target', $targetId);
        }

        $data = $this->mapper->mapTargets($data);

        return reset($data);
    }

    /**
     * Loads all targets that belong to rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target[]
     */
    public function loadRuleTargets(Rule $rule)
    {
        return $this->mapper->mapTargets(
            $this->queryHandler->loadRuleTargetsData($rule->id, $rule->status)
        );
    }

    /**
     * Loads the count of targets within the rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return int
     */
    public function getTargetCount(Rule $rule)
    {
        return $this->queryHandler->getTargetCount($rule->id, $rule->status);
    }

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
    public function loadCondition($conditionId, $status)
    {
        $data = $this->queryHandler->loadConditionData($conditionId, $status);

        if (empty($data)) {
            throw new NotFoundException('condition', $conditionId);
        }

        $data = $this->mapper->mapConditions($data);

        return reset($data);
    }

    /**
     * Loads all conditions that belong to rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition[]
     */
    public function loadRuleConditions(Rule $rule)
    {
        return $this->mapper->mapConditions(
            $this->queryHandler->loadRuleConditionsData($rule->id, $rule->status)
        );
    }

    /**
     * Returns if rule with specified ID exists.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return bool
     */
    public function ruleExists($ruleId, $status)
    {
        return $this->queryHandler->ruleExists($ruleId, $status);
    }

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\API\Values\RuleCreateStruct $ruleCreateStruct
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRule(APIRuleCreateStruct $ruleCreateStruct, $status)
    {
        $createdRuleId = $this->queryHandler->createRule(
            new RuleCreateStruct(
                array(
                    'layoutId' => $ruleCreateStruct->layoutId,
                    'priority' => $ruleCreateStruct->priority !== null ? $ruleCreateStruct->priority : 0,
                    'enabled' => $ruleCreateStruct->enabled !== null ? $ruleCreateStruct->enabled : false,
                    'comment' => $ruleCreateStruct->comment,
                    'status' => $status,
                )
            )
        );

        return $this->loadRule($createdRuleId, $status);
    }

    /**
     * Updates a rule with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function updateRule(Rule $rule, APIRuleUpdateStruct $ruleUpdateStruct)
    {
        $this->queryHandler->updateRule(
            $rule->id,
            $rule->status,
            new RuleUpdateStruct(
                array(
                    'layoutId' => $ruleUpdateStruct->layoutId !== null ? $ruleUpdateStruct->layoutId : $rule->layoutId,
                    'priority' => $ruleUpdateStruct->priority !== null ? $ruleUpdateStruct->priority : $rule->priority,
                    'comment' => $ruleUpdateStruct->comment !== null ? $ruleUpdateStruct->comment : $rule->comment,
                )
            )
        );

        return $this->loadRule($rule->id, $rule->status);
    }

    /**
     * Copies a rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return int The ID of copied rule
     */
    public function copyRule($ruleId, $status = null)
    {
        // First copy rule data

        $ruleData = $this->queryHandler->loadRuleData($ruleId, $status);
        $insertedRuleId = null;

        foreach ($ruleData as $ruleDataRow) {
            $insertedRuleId = $this->queryHandler->createRule(
                new RuleCreateStruct(
                    array(
                        'layoutId' => $ruleDataRow['layout_id'],
                        'priority' => $ruleDataRow['priority'],
                        'enabled' => $ruleDataRow['enabled'],
                        'comment' => $ruleDataRow['comment'],
                        'status' => $ruleDataRow['status'],
                    )
                ),
                $insertedRuleId
            );
        }

        // Then copy rule target data

        $targetData = $this->queryHandler->loadRuleTargetsData($ruleId, $status);
        $targetIdMapping = array();

        foreach ($targetData as $targetDataRow) {
            $insertedTargetId = $this->queryHandler->addTarget(
                new TargetCreateStruct(
                    array(
                        'ruleId' => $insertedRuleId,
                        'status' => $targetDataRow['status'],
                        'identifier' => $targetDataRow['identifier'],
                        'value' => $targetDataRow['value'],
                    )
                ),
                isset($targetIdMapping[$targetDataRow['id']]) ?
                    $targetIdMapping[$targetDataRow['id']] :
                    null
            );

            if (!isset($targetIdMapping[$targetDataRow['id']])) {
                $targetIdMapping[$targetDataRow['id']] = $insertedTargetId;
            }
        }

        // Then copy rule condition data

        $conditionData = $this->queryHandler->loadRuleConditionsData($ruleId, $status);
        $conditionIdMapping = array();

        foreach ($conditionData as $conditionDataRow) {
            $insertedConditionId = $this->queryHandler->addCondition(
                new ConditionCreateStruct(
                    array(
                        'ruleId' => $insertedRuleId,
                        'status' => $conditionDataRow['status'],
                        'identifier' => $conditionDataRow['identifier'],
                        'value' => json_decode($conditionDataRow['value'], true),
                    )
                ),
                isset($conditionIdMapping[$conditionDataRow['id']]) ?
                    $conditionIdMapping[$conditionDataRow['id']] :
                    null
            );

            if (!isset($conditionIdMapping[$conditionDataRow['id']])) {
                $conditionIdMapping[$conditionDataRow['id']] = $insertedConditionId;
            }
        }

        return $insertedRuleId;
    }

    /**
     * Creates a new rule status.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRuleStatus(Rule $rule, $newStatus)
    {
        $ruleData = $this->queryHandler->loadRuleData($rule->id, $rule->status);

        $this->queryHandler->createRule(
            new RuleCreateStruct(
                array(
                    'layoutId' => $ruleData[0]['layout_id'],
                    'priority' => $ruleData[0]['priority'],
                    'enabled' => $ruleData[0]['enabled'],
                    'comment' => $ruleData[0]['comment'],
                    'status' => $newStatus,
                )
            ),
            $ruleData[0]['id']
        );

        $targetData = $this->queryHandler->loadRuleTargetsData($ruleData[0]['id'], $rule->status);
        foreach ($targetData as $targetDataRow) {
            $this->queryHandler->addTarget(
                new TargetCreateStruct(
                    array(
                        'ruleId' => $targetDataRow['rule_id'],
                        'status' => $newStatus,
                        'identifier' => $targetDataRow['identifier'],
                        'value' => $targetDataRow['value'],
                    )
                ),
                $targetDataRow['id']
            );
        }

        $conditionData = $this->queryHandler->loadRuleConditionsData($ruleData[0]['id'], $rule->status);
        foreach ($conditionData as $conditionDataRow) {
            $this->queryHandler->addCondition(
                new ConditionCreateStruct(
                    array(
                        'ruleId' => $conditionDataRow['rule_id'],
                        'status' => $newStatus,
                        'identifier' => $conditionDataRow['identifier'],
                        'value' => json_decode($conditionDataRow['value'], true),
                    )
                ),
                $conditionDataRow['id']
            );
        }

        return $this->loadRule($ruleData[0]['id'], $newStatus);
    }

    /**
     * Deletes a rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     */
    public function deleteRule($ruleId, $status = null)
    {
        $this->queryHandler->deleteRuleTargets($ruleId, $status);
        $this->queryHandler->deleteRuleConditions($ruleId, $status);
        $this->queryHandler->deleteRule($ruleId, $status);
    }

    /**
     * Enables a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     */
    public function enableRule(Rule $rule)
    {
        $this->queryHandler->updateRuleData($rule->id, true);
    }

    /**
     * Disables a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     */
    public function disableRule(Rule $rule)
    {
        $this->queryHandler->updateRuleData($rule->id, false);
    }

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function addTarget(Rule $rule, APITargetCreateStruct $targetCreateStruct)
    {
        $createdTargetId = $this->queryHandler->addTarget(
            new TargetCreateStruct(
                array(
                    'ruleId' => $rule->id,
                    'status' => $rule->status,
                    'identifier' => $targetCreateStruct->identifier,
                    'value' => $targetCreateStruct->value,
                )
            )
        );

        return $this->loadTarget($createdTargetId, $rule->status);
    }

    /**
     * Updates a target with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     * @param \Netgen\BlockManager\API\Values\TargetUpdateStruct $targetUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function updateTarget(Target $target, APITargetUpdateStruct $targetUpdateStruct)
    {
        $this->queryHandler->updateTarget(
            $target->id,
            $target->status,
            new TargetUpdateStruct(
                array(
                    'value' => $targetUpdateStruct->value,
                )
            )
        );

        return $this->loadTarget($target->id, $target->status);
    }

    /**
     * Removes a target.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     */
    public function deleteTarget(Target $target)
    {
        $this->queryHandler->deleteTarget($target->id, $target->status);
    }

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function addCondition(Rule $rule, APIConditionCreateStruct $conditionCreateStruct)
    {
        $createdConditionId = $this->queryHandler->addCondition(
            new ConditionCreateStruct(
                array(
                    'ruleId' => $rule->id,
                    'status' => $rule->status,
                    'identifier' => $conditionCreateStruct->identifier,
                    'value' => $conditionCreateStruct->value,
                )
            )
        );

        return $this->loadCondition($createdConditionId, $rule->status);
    }

    /**
     * Updates a condition with specified ID.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function updateCondition(Condition $condition, APIConditionUpdateStruct $conditionUpdateStruct)
    {
        $this->queryHandler->updateCondition(
            $condition->id,
            $condition->status,
            new ConditionUpdateStruct(
                array(
                    'value' => $conditionUpdateStruct->value,
                )
            )
        );

        return $this->loadCondition($condition->id, $condition->status);
    }

    /**
     * Removes a condition.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     */
    public function deleteCondition(Condition $condition)
    {
        $this->queryHandler->deleteCondition($condition->id, $condition->status);
    }
}
