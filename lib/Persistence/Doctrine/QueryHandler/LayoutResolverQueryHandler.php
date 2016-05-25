<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper;
use Netgen\BlockManager\Persistence\Values\ConditionCreateStruct;
use Netgen\BlockManager\Persistence\Values\ConditionUpdateStruct;
use Netgen\BlockManager\Persistence\Values\RuleCreateStruct;
use Netgen\BlockManager\Persistence\Values\RuleUpdateStruct;
use Netgen\BlockManager\Persistence\Values\TargetCreateStruct;
use Doctrine\DBAL\Types\Type;

class LayoutResolverQueryHandler
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper
     */
    protected $connectionHelper;

    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper
     */
    protected $queryHelper;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\QueryHelper $queryHelper
     */
    public function __construct(ConnectionHelper $connectionHelper, QueryHelper $queryHelper)
    {
        $this->connectionHelper = $connectionHelper;
        $this->queryHelper = $queryHelper;
    }

    /**
     * Returns all data for specified rule.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return array
     */
    public function loadRuleData($ruleId, $status = null)
    {
        $query = $this->getRuleSelectQuery();
        $query->where(
            $query->expr()->eq('r.id', ':id')
        )
        ->setParameter('id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status, 'r.status');
            $query->addOrderBy('r.status', 'ASC');
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Returns all data for all rules.
     *
     * @param int $status
     *
     * @return array
     */
    public function loadRulesData($status = null)
    {
        $query = $this->getRuleSelectQuery();

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status, 'r.status');
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Returns all data for specified target.
     *
     * @param int|string $targetId
     * @param int $status
     *
     * @return array
     */
    public function loadTargetData($targetId, $status = null)
    {
        $query = $this->getTargetSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $targetId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Returns all data for all rule targets.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return array
     */
    public function loadRuleTargetsData($ruleId, $status = null)
    {
        $query = $this->getTargetSelectQuery();
        $query->where(
            $query->expr()->eq('rule_id', ':rule_id')
        )
        ->setParameter('rule_id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Returns the number of targets within the rule.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return int
     */
    public function getTargetCount($ruleId, $status)
    {
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_rule_target')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $ruleId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) ? (int)$data[0]['count'] : 0;
    }

    /**
     * Returns all data for specified condition.
     *
     * @param int|string $conditionId
     * @param int $status
     *
     * @return array
     */
    public function loadConditionData($conditionId, $status = null)
    {
        $query = $this->getConditionSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $conditionId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Returns all data for for all rule conditions.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return array
     */
    public function loadRuleConditionsData($ruleId, $status = null)
    {
        $query = $this->getConditionSelectQuery();
        $query->where(
            $query->expr()->eq('rule_id', ':rule_id')
        )
        ->setParameter('rule_id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
            $query->addOrderBy('status', 'ASC');
        }

        return $query->execute()->fetchAll();
    }

    /**
     * Returns if the specified rule exists.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return bool
     */
    public function ruleExists($ruleId, $status)
    {
        $query = $this->queryHelper->getQuery();
        $query->select('count(*) AS count')
            ->from('ngbm_rule')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\RuleCreateStruct $ruleCreateStruct
     * @param int|string $ruleId
     *
     * @return int
     */
    public function createRule(RuleCreateStruct $ruleCreateStruct, $ruleId = null)
    {
        $query = $this->queryHelper->getQuery()
            ->insert('ngbm_rule')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'layout_id' => ':layout_id',
                    'priority' => ':priority',
                    'comment' => ':comment',
                )
            )
            ->setValue(
                'id',
                $ruleId !== null ? (int)$ruleId : $this->connectionHelper->getAutoIncrementValue('ngbm_rule')
            )
            ->setParameter('status', $ruleCreateStruct->status, Type::INTEGER)
            ->setParameter('layout_id', $ruleCreateStruct->layoutId, Type::INTEGER)
            ->setParameter('priority', $ruleCreateStruct->priority, Type::INTEGER)
            ->setParameter('comment', $ruleCreateStruct->comment, Type::STRING);

        $query->execute();

        $createdRuleId = (int)$this->connectionHelper->lastInsertId('ngbm_rule');

        $query = $this->queryHelper->getQuery()
            ->insert('ngbm_rule_data')
            ->values(
                array(
                    'rule_id' => ':rule_id',
                    'enabled' => ':enabled',
                )
            )
            ->setParameter('rule_id', $createdRuleId, Type::INTEGER)
            ->setParameter('enabled', $ruleCreateStruct->enabled, Type::BOOLEAN);

        $query->execute();

        return $createdRuleId;
    }

    /**
     * Updates a rule.
     *
     * @param int|string $ruleId
     * @param int $status
     * @param \Netgen\BlockManager\Persistence\Values\RuleUpdateStruct $ruleUpdateStruct
     */
    public function updateRule($ruleId, $status, RuleUpdateStruct $ruleUpdateStruct)
    {
        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_rule')
            ->set('layout_id', ':layout_id')
            ->set('priority', ':priority')
            ->set('comment', ':comment')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleId, Type::INTEGER)
            ->setParameter('layout_id', $ruleUpdateStruct->layoutId, Type::INTEGER)
            ->setParameter('priority', $ruleUpdateStruct->priority, Type::INTEGER)
            ->setParameter('comment', $ruleUpdateStruct->comment, Type::STRING);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Updates rule data which is independent of statuses.
     *
     * @param int|string $ruleId
     * @param bool $enabled
     */
    public function updateRuleData($ruleId, $enabled)
    {
        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_rule_data')
            ->set('enabled', ':enabled')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $ruleId, Type::INTEGER)
            ->setParameter('enabled', $enabled, Type::BOOLEAN);

        $query->execute();
    }

    /**
     * Deletes all rule targets.
     *
     * @param int|string $ruleId
     * @param int $status
     */
    public function deleteRuleTargets($ruleId, $status = null)
    {
        $query = $this->queryHelper->getQuery();
        $query
            ->delete('ngbm_rule_target')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Delete all rule conditions.
     *
     * @param int|string $ruleId
     * @param int $status
     */
    public function deleteRuleConditions($ruleId, $status = null)
    {
        $query = $this->queryHelper->getQuery();
        $query
            ->delete('ngbm_rule_condition')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Deletes a rule.
     *
     * @param int|string $ruleId
     * @param int $status
     */
    public function deleteRule($ruleId, $status = null)
    {
        $query = $this->queryHelper->getQuery();
        $query->delete('ngbm_rule')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->queryHelper->applyStatusCondition($query, $status);
        }

        $query->execute();
    }

    /**
     * Adds a target to rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\TargetCreateStruct $targetCreateStruct
     * @param int|string $targetId
     *
     * @return int
     */
    public function addTarget(TargetCreateStruct $targetCreateStruct, $targetId = null)
    {
        $query = $this->queryHelper->getQuery()
            ->insert('ngbm_rule_target')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'rule_id' => ':rule_id',
                    'identifier' => ':identifier',
                    'value' => ':value',
                )
            )
            ->setValue(
                'id',
                $targetId !== null ? (int)$targetId : $this->connectionHelper->getAutoIncrementValue('ngbm_rule_target')
            )
            ->setParameter('status', $targetCreateStruct->status, Type::INTEGER)
            ->setParameter('rule_id', $targetCreateStruct->ruleId, Type::INTEGER)
            ->setParameter('identifier', $targetCreateStruct->identifier, Type::STRING)
            ->setParameter('value', $targetCreateStruct->value, is_array($targetCreateStruct->value) ? Type::JSON_ARRAY : Type::STRING);

        $query->execute();

        return (int)$this->connectionHelper->lastInsertId('ngbm_rule_target');
    }

    /**
     * Deletes a target.
     *
     * @param int|string $targetId
     * @param int $status
     */
    public function deleteTarget($targetId, $status)
    {
        $query = $this->queryHelper->getQuery();

        $query->delete('ngbm_rule_target')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $targetId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Adds a condition to rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\ConditionCreateStruct $conditionCreateStruct
     * @param int|string $conditionId
     *
     * @return int
     */
    public function addCondition(ConditionCreateStruct $conditionCreateStruct, $conditionId = null)
    {
        $query = $this->queryHelper->getQuery()
            ->insert('ngbm_rule_condition')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'rule_id' => ':rule_id',
                    'identifier' => ':identifier',
                    'value' => ':value',
                )
            )
            ->setValue(
                'id',
                $conditionId !== null ? (int)$conditionId : $this->connectionHelper->getAutoIncrementValue('ngbm_rule_condition')
            )
            ->setParameter('status', $conditionCreateStruct->status, Type::INTEGER)
            ->setParameter('rule_id', $conditionCreateStruct->ruleId, Type::INTEGER)
            ->setParameter('identifier', $conditionCreateStruct->identifier, Type::STRING)
            ->setParameter('value', $conditionCreateStruct->value, is_array($conditionCreateStruct->value) ? Type::JSON_ARRAY : Type::STRING);

        $query->execute();

        return (int)$this->connectionHelper->lastInsertId('ngbm_rule_condition');
    }

    /**
     * Updates a condition.
     *
     * @param int|string $conditionId
     * @param int $status
     * @param \Netgen\BlockManager\Persistence\Values\ConditionUpdateStruct $conditionUpdateStruct
     */
    public function updateCondition($conditionId, $status, ConditionUpdateStruct $conditionUpdateStruct)
    {
        $query = $this->queryHelper->getQuery();
        $query
            ->update('ngbm_rule_condition')
            ->set('value', ':value')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $conditionId, Type::INTEGER)
            ->setParameter('value', $conditionUpdateStruct->value, is_array($conditionUpdateStruct->value) ? Type::JSON_ARRAY : Type::STRING);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Deletes a condition.
     *
     * @param int|string $conditionId
     * @param int $status
     */
    public function deleteCondition($conditionId, $status)
    {
        $query = $this->queryHelper->getQuery();

        $query->delete('ngbm_rule_condition')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $conditionId, Type::INTEGER);

        $this->queryHelper->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Builds and returns a rule database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getRuleSelectQuery()
    {
        $query = $this->queryHelper->getQuery();
        $query->select('r.id', 'r.status', 'r.layout_id', 'r.priority', 'r.comment', 'rd.enabled')
            ->from('ngbm_rule', 'r')
            ->leftJoin('r', 'ngbm_rule_data', 'rd', 'rd.rule_id = r.id');

        return $query;
    }

    /**
     * Builds and returns a target database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getTargetSelectQuery()
    {
        $query = $this->queryHelper->getQuery();
        $query->select('id', 'status', 'rule_id', 'identifier', 'value')
            ->from('ngbm_rule_target');

        return $query;
    }

    /**
     * Builds and returns a condition database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getConditionSelectQuery()
    {
        $query = $this->queryHelper->getQuery();
        $query->select('id', 'status', 'rule_id', 'identifier', 'value')
            ->from('ngbm_rule_condition');

        return $query;
    }
}
