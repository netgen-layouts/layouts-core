<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Doctrine\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Type;
use Netgen\Layouts\Exception\Persistence\TargetHandlerException;
use Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper;
use Netgen\Layouts\Persistence\Values\Layout\Layout;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Condition;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Rule;
use Netgen\Layouts\Persistence\Values\LayoutResolver\Target;
use Netgen\Layouts\Persistence\Values\Value;
use PDO;

final class LayoutResolverQueryHandler extends QueryHandler
{
    /**
     * @var \Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface[]
     */
    private $targetHandlers;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     * @param \Netgen\Layouts\Persistence\Doctrine\Helper\ConnectionHelper $connectionHelper
     * @param \Netgen\Layouts\Persistence\Doctrine\QueryHandler\TargetHandlerInterface[] $targetHandlers
     */
    public function __construct(Connection $connection, ConnectionHelper $connectionHelper, array $targetHandlers)
    {
        $this->targetHandlers = array_filter(
            $targetHandlers,
            static function (TargetHandlerInterface $targetHandler): bool {
                return true;
            }
        );

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
    public function loadRuleData($ruleId, int $status): array
    {
        $query = $this->getRuleSelectQuery();
        $query->where(
            $query->expr()->eq('r.id', ':id')
        )
        ->setParameter('id', $ruleId, Type::INTEGER);

        $this->applyStatusCondition($query, $status, 'r.status');

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all data for all rules.
     */
    public function loadRulesData(int $status, ?Layout $layout = null, int $offset = 0, ?int $limit = null): array
    {
        $query = $this->getRuleSelectQuery();

        if ($layout instanceof Layout) {
            $query->andWhere(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layout->id, Type::INTEGER);
        }

        $query->addOrderBy('rd.priority', 'DESC');

        $this->applyStatusCondition($query, $status, 'r.status');
        $this->applyOffsetAndLimit($query, $offset, $limit);

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the number of rules.
     */
    public function getRuleCount(int $ruleStatus, ?Layout $layout = null): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule');

        if ($layout instanceof Layout) {
            $query->andWhere(
                $query->expr()->eq('layout_id', ':layout_id')
            )
            ->setParameter('layout_id', $layout->id, Type::INTEGER);
        }

        $this->applyStatusCondition($query, $ruleStatus);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all rule data for rules that match specified target type and value.
     *
     * @param string $targetType
     * @param mixed $targetValue
     *
     * @return array
     */
    public function matchRules(string $targetType, $targetValue): array
    {
        $query = $this->getRuleSelectQuery();
        $query
            ->innerJoin(
                'r',
                'nglayouts_rule_target',
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

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all data for specified target.
     *
     * @param int|string $targetId
     * @param int $status
     *
     * @return array
     */
    public function loadTargetData($targetId, int $status): array
    {
        $query = $this->getTargetSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $targetId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all data for all rule targets.
     */
    public function loadRuleTargetsData(Rule $rule): array
    {
        $query = $this->getTargetSelectQuery();
        $query->where(
            $query->expr()->eq('rule_id', ':rule_id')
        )
        ->setParameter('rule_id', $rule->id, Type::INTEGER)
        ->orderBy('id', 'ASC');

        $this->applyStatusCondition($query, $rule->status);

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns the number of targets within the rule.
     */
    public function getTargetCount(Rule $rule): int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule_target')
            ->where(
                $query->expr()->eq('rule_id', ':rule_id')
            )
            ->setParameter('rule_id', $rule->id, Type::INTEGER);

        $this->applyStatusCondition($query, $rule->status);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0);
    }

    /**
     * Returns all data for specified condition.
     *
     * @param int|string $conditionId
     * @param int $status
     *
     * @return array
     */
    public function loadConditionData($conditionId, int $status): array
    {
        $query = $this->getConditionSelectQuery();
        $query->where(
            $query->expr()->eq('id', ':id')
        )
        ->setParameter('id', $conditionId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns all data for for all rule conditions.
     */
    public function loadRuleConditionsData(Rule $rule): array
    {
        $query = $this->getConditionSelectQuery();
        $query->where(
            $query->expr()->eq('rule_id', ':rule_id')
        )
        ->setParameter('rule_id', $rule->id, Type::INTEGER)
        ->orderBy('id', 'ASC');

        $this->applyStatusCondition($query, $rule->status);

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns if the specified rule exists.
     *
     * @param int|string $ruleId
     * @param int $status
     *
     * @return bool
     */
    public function ruleExists($ruleId, ?int $status = null): bool
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('count(*) AS count')
            ->from('nglayouts_rule')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $ruleId, Type::INTEGER);

        if ($status !== null) {
            $this->applyStatusCondition($query, $status);
        }

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return (int) ($data[0]['count'] ?? 0) > 0;
    }

    /**
     * Returns the lowest priority from the list of all the rules.
     */
    public function getLowestRulePriority(): ?int
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('priority')
            ->from('nglayouts_rule_data');

        $query->addOrderBy('priority', 'ASC');
        $this->applyOffsetAndLimit($query, 0, 1);

        $data = $query->execute()->fetchAll(PDO::FETCH_ASSOC);

        return isset($data[0]['priority']) ? (int) $data[0]['priority'] : null;
    }

    /**
     * Creates a rule.
     */
    public function createRule(Rule $rule): Rule
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule')
            ->values(
                [
                    'id' => ':id',
                    'status' => ':status',
                    'layout_id' => ':layout_id',
                    'comment' => ':comment',
                ]
            )
            ->setValue(
                'id',
                $rule->id !== null ?
                    (int) $rule->id :
                    $this->connectionHelper->getAutoIncrementValue('nglayouts_rule')
            )
            ->setParameter('status', $rule->status, Type::INTEGER)
            ->setParameter('layout_id', $rule->layoutId, Type::INTEGER)
            ->setParameter('comment', $rule->comment, Type::STRING);

        $query->execute();

        if ($rule->id === null) {
            $rule->id = (int) $this->connectionHelper->lastInsertId('nglayouts_rule');

            $query = $this->connection->createQueryBuilder()
                ->insert('nglayouts_rule_data')
                ->values(
                    [
                        'rule_id' => ':rule_id',
                        'enabled' => ':enabled',
                        'priority' => ':priority',
                    ]
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
     */
    public function updateRule(Rule $rule): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule')
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
     */
    public function updateRuleData(Rule $rule): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_data')
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
    public function deleteRuleTargets($ruleId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_target')
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
    public function deleteRuleConditions($ruleId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete('nglayouts_rule_condition')
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
    public function deleteRule($ruleId, ?int $status = null): void
    {
        $query = $this->connection->createQueryBuilder();
        $query->delete('nglayouts_rule')
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
            $query->delete('nglayouts_rule_data')
                ->where(
                    $query->expr()->eq('rule_id', ':rule_id')
                )
                ->setParameter('rule_id', $ruleId, Type::INTEGER);

            $query->execute();
        }
    }

    /**
     * Adds a target.
     */
    public function addTarget(Target $target): Target
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule_target')
            ->values(
                [
                    'id' => ':id',
                    'status' => ':status',
                    'rule_id' => ':rule_id',
                    'type' => ':type',
                    'value' => ':value',
                ]
            )
            ->setValue(
                'id',
                $target->id !== null ?
                    (int) $target->id :
                    $this->connectionHelper->getAutoIncrementValue('nglayouts_rule_target')
            )
            ->setParameter('status', $target->status, Type::INTEGER)
            ->setParameter('rule_id', $target->ruleId, Type::INTEGER)
            ->setParameter('type', $target->type, Type::STRING)
            ->setParameter('value', $target->value, is_array($target->value) ? Type::JSON_ARRAY : Type::STRING);

        $query->execute();

        $target->id = $target->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_rule_target');

        return $target;
    }

    /**
     * Updates a target.
     */
    public function updateTarget(Target $target): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_target')
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
    public function deleteTarget($targetId, int $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_target')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $targetId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Adds a condition.
     */
    public function addCondition(Condition $condition): Condition
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('nglayouts_rule_condition')
            ->values(
                [
                    'id' => ':id',
                    'status' => ':status',
                    'rule_id' => ':rule_id',
                    'type' => ':type',
                    'value' => ':value',
                ]
            )
            ->setValue(
                'id',
                $condition->id !== null ?
                    (int) $condition->id :
                    $this->connectionHelper->getAutoIncrementValue('nglayouts_rule_condition')
            )
            ->setParameter('status', $condition->status, Type::INTEGER)
            ->setParameter('rule_id', $condition->ruleId, Type::INTEGER)
            ->setParameter('type', $condition->type, Type::STRING)
            ->setParameter('value', json_encode($condition->value), Type::STRING);

        $query->execute();

        $condition->id = $condition->id ?? (int) $this->connectionHelper->lastInsertId('nglayouts_rule_condition');

        return $condition;
    }

    /**
     * Updates a condition.
     */
    public function updateCondition(Condition $condition): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->update('nglayouts_rule_condition')
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
    public function deleteCondition($conditionId, int $status): void
    {
        $query = $this->connection->createQueryBuilder();

        $query->delete('nglayouts_rule_condition')
            ->where(
                $query->expr()->eq('id', ':id')
            )
            ->setParameter('id', $conditionId, Type::INTEGER);

        $this->applyStatusCondition($query, $status);

        $query->execute();
    }

    /**
     * Builds and returns a rule database SELECT query.
     */
    private function getRuleSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT r.*', 'rd.*, l.uuid AS layout_uuid')
            ->from('nglayouts_rule', 'r')
            ->innerJoin(
                'r',
                'nglayouts_rule_data',
                'rd',
                $query->expr()->eq('rd.rule_id', 'r.id')
            )->leftJoin(
                'r',
                'nglayouts_layout',
                'l',
                $query->expr()->andX(
                    $query->expr()->eq('r.layout_id', 'l.id'),
                    $query->expr()->eq('l.status', Value::STATUS_PUBLISHED)
                )
            );

        return $query;
    }

    /**
     * Builds and returns a target database SELECT query.
     */
    private function getTargetSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT nglayouts_rule_target.*')
            ->from('nglayouts_rule_target');

        return $query;
    }

    /**
     * Builds and returns a condition database SELECT query.
     */
    private function getConditionSelectQuery(): QueryBuilder
    {
        $query = $this->connection->createQueryBuilder();
        $query->select('DISTINCT nglayouts_rule_condition.*')
            ->from('nglayouts_rule_condition');

        return $query;
    }
}
