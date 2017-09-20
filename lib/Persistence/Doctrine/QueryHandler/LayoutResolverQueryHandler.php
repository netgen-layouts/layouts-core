<?php

namespace Netgen\BlockManager\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Netgen\BlockManager\Exception\InvalidInterfaceException;
use Netgen\BlockManager\Exception\Persistence\TargetHandlerException;
use Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Persistence\Values\LayoutResolver\Target;
use Netgen\BlockManager\Persistence\Values\Value;

class LayoutResolverQueryHandler extends QueryHandler
{
    /**
     * @var \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler[]
     */
    private $targetHandlers = array();

    /**
     * Constructor.
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\BlockManager\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\BlockManager\Persistence\Doctrine\QueryHandler\LayoutResolver\TargetHandler[] $targetHandlers
     */
    public function __construct(Connection $connection, ConnectionHelper $connectionHelper, array $targetHandlers = array())
    {
        foreach ($targetHandlers as $targetHandler) {
            if (!$targetHandler instanceof TargetHandler) {
                throw new InvalidInterfaceException(
                    'Target handler',
                    get_class($targetHandler),
                    TargetHandler::class
                );
            }
        }

        $this->targetHandlers = $targetHandlers;

        parent::__construct($connection, $connectionHelper);
    }

    /**
     * Returns all data for specified rule.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return array
     */
    public function loadRuleData($ruleId, $status)
    {
        $query = $this->getRuleSelectQuery();
        $query->where(
            $query->expr()->eq('r.id', ':id')
        )
        ->setParameter('id', $ruleId, Type::INTEGER);

        $this->applyStatusCondition($query, $status, 'r.status');

        return $query->execute()->fetchAll();
    }

    /**
     * Returns all data for all rules.
     *
     * @param int $status
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function loadRulesData($status, $offset = 0, $limit = null)
    {
        $query = $this->getRuleSelectQuery();
        $query->addOrderBy('rd.priority', 'DESC');

        $this->applyStatusCondition($query, $status, 'r.status');
        $this->applyOffsetAndLimit($query, $offset, $limit);

        return $query->execute()->fetchAll();
    }

    /**
     * Returns the number of rules pointing to provided layout.
     *
     * @param int|string $layoutId
     * @param int $ruleStatus
     *
     * @return int
     */
    public function getRuleCount($layoutId, $ruleStatus)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_rule')
            ->where(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layoutId, Type::INTEGER);

        $this->applyStatusCondition($query, $ruleStatus);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) ? (int) $data[0]['count'] : 0;
    }

    /**
     * Returns all rule data for rules that match specified target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return array
     */
    public function matchRules($targetType, $targetValue)
    {
        $query = $this->getRuleSelectQuery();
        $query
            ->innerJoin(
                'r',
                'ngbm_rule_target',
                'rt',
                $query->expr()->eq('r.id', 'rt.rule_id')
            )
            ->where(
                $query->expr()->eq('rd.enabled', ':enabled'),
                $query->expr()->eq('rt.type', ':target_type')
            )
            ->setParameter('target_type', $targetType, Type::STRING)
            ->setParameter('enabled', true, Type::BOOLEAN)
            ->addOrderBy('rd.priority', 'DESC');

        $this->applyStatusCondition($query, Value::STATUS_PUBLISHED, 'r.status');
        $this->applyStatusCondition($query, Value::STATUS_PUBLISHED, 'rt.status');

        if (!isset($this->targetHandlers[$targetType])) {
            throw TargetHandlerException::noTargetHandler('Doctrine', $targetType);
        }

        $this->targetHandlers[$targetType]->handleQuery($query, $targetValue);

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
    public function loadTargetData($targetId, $status)
    {
        $query = $this->getTargetSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $targetId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        return $query->execute()->fetchAll();
    }

    /**
     * Returns all data for all rule targets.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return array
     */
    public function loadRuleTargetsData(Rule $rule)
    {
        $query = $this->getTargetSelectQuery();
        $query->where(
            $query->expr()->eq('rule_id', ':rule_id')
        )
        ->setParameter('rule_id', $rule->id, Type::INTEGER)
        ->orderBy('id', 'ASC');

        $this->applyStatusCondition($query, $rule->status);

        return $query->execute()->fetchAll();
    }

    /**
     * Returns the number of targets within the rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return int
     */
    public function getTargetCount(Rule $rule)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_rule_target')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $rule->id, Type::INTEGER);

        $this->applyStatusCondition($query, $rule->status);

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) ? (int) $data[0]['count'] : 0;
    }

    /**
     * Returns all data for specified condition.
     *
     * @param int|string $conditionId
     * @param int $status
     *
     * @return array
     */
    public function loadConditionData($conditionId, $status)
    {
        $query = $this->getConditionSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $conditionId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        return $query->execute()->fetchAll();
    }

    /**
     * Returns all data for for all rule conditions.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return array
     */
    public function loadRuleConditionsData(Rule $rule)
    {
        $query = $this->getConditionSelectQuery();
        $query->where(
            $query->expr()->eq('rule_id', ':rule_id')
        )
        ->setParameter('rule_id', $rule->id, Type::INTEGER)
        ->orderBy('id', 'ASC');

        $this->applyStatusCondition($query, $rule->status);

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
    public function ruleExists($ruleId, $status = null)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('ngbm_rule')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAll();

        return isset($data[0]['count']) && $data[0]['count'] > 0;
    }

    /**
     * Creates a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule
     */
    public function createRule(Rule $rule)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_rule')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'layout_id' => ':layout_id',
                    'comment' => ':comment',
                )
            )
            ->setValue(
                'id',
                $rule->id !== null ?
                    (int) $rule->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_rule')
            )
            ->setParameter('status', $rule->status, Type::INTEGER)
            ->setParameter('layout_id', $rule->layoutId, Type::INTEGER)
            ->setParameter('comment', $rule->comment, Type::STRING);

        $query->execute();

        if ($rule->id === null) {
            $rule->id = (int) $this->connectionHelper->lastInsertId('ngbm_rule');

            $query = $this->connection->createQueryBuilder()
                ->insert('ngbm_rule_data')
                ->values(
                    array(
                        'rule_id' => ':rule_id',
                        'enabled' => ':enabled',
                        'priority' => ':priority',
                    )
                )
                ->setParameter('rule_id', $rule->id, Type::INTEGER)
                ->setParameter('enabled', $rule->enabled, Type::BOOLEAN)
                ->setParameter('priority', $rule->priority, Type::INTEGER);

            $query->execute();
        }

        return $rule;
    }

    /**
     * Updates a rule.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     */
    public function updateRule(Rule $rule)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_rule')
            ->set('layout_id', ':layout_id')
            ->set('comment', ':comment')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $rule->id, Type::INTEGER)
            ->setParameter('layout_id', $rule->layoutId, Type::INTEGER)
            ->setParameter('comment', $rule->comment, Type::STRING);

        $this->applyStatusCondition($query, $rule->status);

        $query->execute();
    }

    /**
     * Updates rule data which is independent of statuses.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Rule $rule
     */
    public function updateRuleData(Rule $rule)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_rule_data')
            ->set('enabled', ':enabled')
            ->set('priority', ':priority')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $rule->id, Type::INTEGER)
            ->setParameter('enabled', $rule->enabled, Type::BOOLEAN)
            ->setParameter('priority', $rule->priority, Type::INTEGER);

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
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('ngbm_rule_target')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
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
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('ngbm_rule_condition')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
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
        $query = $this->connection->createQueryBuilder();
        $query->delete('ngbm_rule')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $query->execute();

        if (!$this->ruleExists($ruleId)) {
            $query = $this->connection->createQueryBuilder();
            $query->delete('ngbm_rule_data')
                ->where(
                    $query->expr()->eq('rule_id', ':rule_id')
                )
                ->setParameter('rule_id', $ruleId, Type::INTEGER);

            $query->execute();
        }
    }

    /**
     * Adds a target.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target
     */
    public function addTarget(Target $target)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_rule_target')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'rule_id' => ':rule_id',
                    'type' => ':type',
                    'value' => ':value',
                )
            )
            ->setValue(
                'id',
                $target->id !== null ?
                    (int) $target->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_rule_target')
            )
            ->setParameter('status', $target->status, Type::INTEGER)
            ->setParameter('rule_id', $target->ruleId, Type::INTEGER)
            ->setParameter('type', $target->type, Type::STRING)
            ->setParameter('value', $target->value, is_array($target->value) ? Type::JSON_ARRAY : Type::STRING);

        $query->execute();

        if ($target->id === null) {
            $target->id = (int) $this->connectionHelper->lastInsertId('ngbm_rule_target');
        }

        return $target;
    }

    /**
     * Updates a target.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Target $target
     */
    public function updateTarget(Target $target)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_rule_target')
            ->set('rule_id', ':rule_id')
            ->set('type', ':type')
            ->set('value', ':value')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $target->id, Type::INTEGER)
            ->setParameter('rule_id', $target->ruleId, Type::INTEGER)
            ->setParameter('type', $target->type, Type::STRING)
            ->setParameter('value', $target->value, is_array($target->value) ? Type::JSON_ARRAY : Type::STRING);

        $this->applyStatusCondition($query, $target->status);

        $query->execute();
    }

    /**
     * Deletes a target.
     *
     * @param int|string $targetId
     * @param int $status
     */
    public function deleteTarget($targetId, $status)
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_rule_target')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $targetId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Adds a condition.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     *
     * @return \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition
     */
    public function addCondition(Condition $condition)
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('ngbm_rule_condition')
            ->values(
                array(
                    'id' => ':id',
                    'status' => ':status',
                    'rule_id' => ':rule_id',
                    'type' => ':type',
                    'value' => ':value',
                )
            )
            ->setValue(
                'id',
                $condition->id !== null ?
                    (int) $condition->id :
                    $this->connectionHelper->getAutoIncrementValue('ngbm_rule_condition')
            )
            ->setParameter('status', $condition->status, Type::INTEGER)
            ->setParameter('rule_id', $condition->ruleId, Type::INTEGER)
            ->setParameter('type', $condition->type, Type::STRING)
            ->setParameter('value', json_encode($condition->value), Type::STRING);

        $query->execute();

        if ($condition->id === null) {
            $condition->id = (int) $this->connectionHelper->lastInsertId('ngbm_rule_condition');
        }

        return $condition;
    }

    /**
     * Updates a condition.
     *
     * @param \Netgen\BlockManager\Persistence\Values\LayoutResolver\Condition $condition
     */
    public function updateCondition(Condition $condition)
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('ngbm_rule_condition')
            ->set('rule_id', ':rule_id')
            ->set('type', ':type')
            ->set('value', ':value')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $condition->id, Type::INTEGER)
            ->setParameter('rule_id', $condition->ruleId, Type::INTEGER)
            ->setParameter('type', $condition->type, Type::STRING)
            ->setParameter('value', json_encode($condition->value), Type::STRING);

        $this->applyStatusCondition($query, $condition->status);

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
        $query = $this->connection->createQueryBuilder();

        $query->delete('ngbm_rule_condition')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $conditionId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Builds and returns a rule database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getRuleSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT r.*', 'rd.*')
            ->from('ngbm_rule', 'r')
            ->innerJoin(
                'r',
                'ngbm_rule_data',
                'rd',
                $query->expr()->eq('rd.rule_id', 'r.id')
            );

        return $query;
    }

    /**
     * Builds and returns a target database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getTargetSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT ngbm_rule_target.*')
            ->from('ngbm_rule_target');

        return $query;
    }

    /**
     * Builds and returns a condition database SELECT query.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getConditionSelectQuery()
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT ngbm_rule_condition.*')
            ->from('ngbm_rule_condition');

        return $query;
    }
}
