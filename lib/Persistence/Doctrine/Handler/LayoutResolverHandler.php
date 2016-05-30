<?php

namespace Netgen\BlockManager\Persistence\Doctrine\Handler;

use Netgen\BlockManager\API\Values\ConditionCreateStruct as APIConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\ConditionCreateStruct;
use Netgen\BlockManager\API\Values\ConditionUpdateStruct as APIConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\ConditionUpdateStruct;
use Netgen\BlockManager\API\Values\RuleCreateStruct as APIRuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\RuleCreateStruct;
use Netgen\BlockManager\API\Values\RuleUpdateStruct as APIRuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\RuleUpdateStruct;
use Netgen\BlockManager\API\Values\TargetCreateStruct as APITargetCreateStruct;
use Netgen\BlockManager\Persistence\Values\TargetCreateStruct;
use Netgen\BlockManager\Persistence\Doctrine\Mapper\LayoutResolverMapper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolverQueryHandler;
use Netgen\BlockManager\Persistence\Handler\LayoutResolverHandler as LayoutResolverHandlerInterface;
use Netgen\BlockManager\Exception\NotFoundException;

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
     * @param int|string $ruleId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target[]
     */
    public function loadRuleTargets($ruleId, $status)
    {
        return $this->mapper->mapTargets(
            $this->queryHandler->loadRuleTargetsData($ruleId, $status)
        );
    }

    /**
     * Loads the count of targets within the rule with specified ID.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return int
     */
    public function getTargetCount($ruleId, $status)
    {
        return $this->queryHandler->getTargetCount($ruleId, $status);
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
     * @param int|string $ruleId
     * @param int $status
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition[]
     */
    public function loadRuleConditions($ruleId, $status)
    {
        return $this->mapper->mapConditions(
            $this->queryHandler->loadRuleConditionsData($ruleId, $status)
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
     * @param int|string $ruleId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\RuleUpdateStruct $ruleUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function updateRule($ruleId, $status, APIRuleUpdateStruct $ruleUpdateStruct)
    {
        $originalRule = $this->loadRule($ruleId, $status);

        $this->queryHandler->updateRule(
            $ruleId,
            $status,
            new RuleUpdateStruct(
                array(
                    'layoutId' => $ruleUpdateStruct->layoutId !== null ? $ruleUpdateStruct->layoutId : $originalRule->layoutId,
                    'priority' => $ruleUpdateStruct->priority !== null ? $ruleUpdateStruct->priority : $originalRule->priority,
                    'comment' => $ruleUpdateStruct->comment !== null ? $ruleUpdateStruct->comment : $originalRule->comment,
                )
            )
        );

        return $this->loadRule($ruleId, $status);
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
     * @param int|string $ruleId
     * @param int $status
     * @param int $newStatus
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRuleStatus($ruleId, $status, $newStatus)
    {
        $ruleData = $this->queryHandler->loadRuleData($ruleId, $status);

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

        $targetData = $this->queryHandler->loadRuleTargetsData($ruleData[0]['id'], $status);
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

        $conditionData = $this->queryHandler->loadRuleConditionsData($ruleData[0]['id'], $status);
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
     * @param int|string $ruleId
     */
    public function enableRule($ruleId)
    {
        $this->queryHandler->updateRuleData($ruleId, true);
    }

    /**
     * Disables a rule.
     *
     * @param int|string $ruleId
     */
    public function disableRule($ruleId)
    {
        $this->queryHandler->updateRuleData($ruleId, false);
    }

    /**
     * Adds a target to rule.
     *
     * @param int|string $ruleId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\TargetCreateStruct $targetCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function addTarget($ruleId, $status, APITargetCreateStruct $targetCreateStruct)
    {
        $createdTargetId = $this->queryHandler->addTarget(
            new TargetCreateStruct(
                array(
                    'ruleId' => $ruleId,
                    'status' => $status,
                    'identifier' => $targetCreateStruct->identifier,
                    'value' => $targetCreateStruct->value,
                )
            )
        );

        return $this->loadTarget($createdTargetId, $status);
    }

    /**
     * Removes a target.
     *
     * @param int|string $targetId
     * @param int $status
     */
    public function deleteTarget($targetId, $status)
    {
        $this->queryHandler->deleteTarget($targetId, $status);
    }

    /**
     * Adds a condition to rule.
     *
     * @param int|string $ruleId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\ConditionCreateStruct $conditionCreateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function addCondition($ruleId, $status, APIConditionCreateStruct $conditionCreateStruct)
    {
        $createdConditionId = $this->queryHandler->addCondition(
            new ConditionCreateStruct(
                array(
                    'ruleId' => $ruleId,
                    'status' => $status,
                    'identifier' => $conditionCreateStruct->identifier,
                    'value' => $conditionCreateStruct->value,
                )
            )
        );

        return $this->loadCondition($createdConditionId, $status);
    }

    /**
     * Updates a condition with specified ID.
     *
     * @param int|string $conditionId
     * @param int $status
     * @param \Netgen\BlockManager\API\Values\ConditionUpdateStruct $conditionUpdateStruct
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function updateCondition($conditionId, $status, APIConditionUpdateStruct $conditionUpdateStruct)
    {
        $this->queryHandler->updateCondition(
            $conditionId,
            $status,
            new ConditionUpdateStruct(
                array(
                    'value' => $conditionUpdateStruct->value,
                )
            )
        );

        return $this->loadCondition($conditionId, $status);
    }

    /**
     * Removes a condition.
     *
     * @param int|string $conditionId
     * @param int $status
     */
    public function deleteCondition($conditionId, $status)
    {
        $this->queryHandler->deleteCondition($conditionId, $status);
    }
}
